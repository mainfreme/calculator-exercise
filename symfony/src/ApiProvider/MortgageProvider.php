<?php

namespace App\ApiProvider;

use ApiPlatform\Exception\InvalidUriVariableException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\ApiRequest\MortgageRequest;
use App\ApiRequest\MortgageV2Request;
use App\ApiResource\MortgageResource;
use App\Service\Installment\DummyInstallment;
use App\Service\Mortgage\MortgageCalculator;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\BodyRendererInterface;

class MortgageProvider implements ProviderInterface
{
    public ValidatorInterface $validation;
    private MortgageCalculator $mortgageCalculator;
    private MailerInterface $mailer;
    private BodyRendererInterface $bodyRenderer;

    public function __construct(
        ValidatorInterface $validation,
        MortgageCalculator $mortgageCalculator,
        DummyInstallment $installment,
        MailerInterface $mailer, BodyRendererInterface $bodyRenderer
    ) {
        $this->validation = $validation;
        $this->installment = $installment;
        $this->mortgageCalculator = $mortgageCalculator;
        $this->mailer = $mailer;
        $this->bodyRenderer = $bodyRenderer;
    }

    /**
     * @throws InvalidUriVariableException
     * @throws RoundingNecessaryException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): MortgageResource
    {
        $filters = $context['filters'] ?? [];
        $input = $operation->getInput();
        $isV2 = \is_array($input) && ($input['class'] ?? null) === MortgageV2Request::class;

        try {
            $request = $isV2
                ? new MortgageV2Request(...$filters)
                : new MortgageRequest(...$filters);
        } catch (\InvalidArgumentException $exception) {
            throw new InvalidUriVariableException();
        }

        $this->validation->validate($request);

        $result = $this->mortgageCalculator->calculate(
            creditValue: $request->creditValue,
            period: $request->period,
            margin: $request->margin,
            provision: $request->provision
        );

        $mortgageResource = new MortgageResource(
            initialCostValue: BigDecimal::of($result['initialCost'])->toScale(2, RoundingMode::HALF_UP),
            totalValue: BigDecimal::of($request->creditValue)
                ->plus($result['initialCost'])
                ->plus($result['totalCost'])
                ->toScale(2, RoundingMode::HALF_UP),
            totalCostValue: BigDecimal::of($result['totalCost'])
                ->plus($result['initialCost'])
                ->toScale(2, RoundingMode::HALF_UP),
            installmentValue: BigDecimal::of($this->installment->calculate(
                $request->creditValue,
                $request->period,
                $request->margin,
                $request->provision)['totalValue']
            )->toScale(2, RoundingMode::HALF_UP)
        );

        if (null != $request->email) {
            $this->sendMail($request->email, [
                'creditValue' => $request->creditValue,
                'period' => $request->period,
                'margin' => $request->margin,
                'provision' => $request->provision,
                'age' => $request->age,
                'secureValue' => $request->secureValue,
                'name' => $request->email,
                'initialCostValue' => $mortgageResource->initialCostValue,
                'totalCostValue' => $mortgageResource->totalCostValue,
                'installmentValue' => $mortgageResource->installmentValue,
            ]);
        }

        return $mortgageResource;
    }

    public function sendMail(string $emailTo, array $params = [])
    {
        try {
            $email = (new TemplatedEmail())
                ->subject('Szczegóły kredytu hipotecznego')
                ->to($emailTo)
                ->from('mail@speedfin.pl')
                ->sender('mail@speedfin.pl')
                ->htmlTemplate('email/mortgage.html.twig')
                ->context($params);
            $this->bodyRenderer->render($email);

            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            // @todo it would be good to log somehow that email failed
        }
    }
}
