<?php

namespace App\ApiProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\MortgageSendEmailResource;
use App\ApiRequest\MortgageSendEmailRequest;
use App\Message\SendMortgageEmailMessage;
use App\Service\Installment\Dto\InstallmentDto;
use App\Service\Installment\DummyInstallment;
use App\Service\Mortgage\MortgageEmailTemplateContextBuilder;
use App\Service\Mortgage\MortgageRequestResolver;
use App\Service\Mortgage\MortgageResourceAssembler;
use Symfony\Component\Messenger\MessageBusInterface;

class MortageEmailProvider implements ProviderInterface
{
    public function __construct(
        private MortgageRequestResolver $mortgageRequestResolver,
        private DummyInstallment $installment,
        private MortgageResourceAssembler $mortgageResourceAssembler,
        private MortgageEmailTemplateContextBuilder $templateContextBuilder,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): MortgageSendEmailResource
    {
        $resolution = $this->mortgageRequestResolver->resolve($operation, $context);
        $request = $resolution->request;

        if (!$request instanceof MortgageSendEmailRequest) {
            throw new \InvalidArgumentException('Expected MortgageSendEmailRequest');
        }

        $installment = $this->installment->calculate(
            new InstallmentDto(
                creditValue: $request->creditValue,
                period: $request->period,
                margin: $request->margin,
                provision: $request->provision
            )
        );

        $dto = $this->mortgageResourceAssembler->assemble($resolution, $installment);
        $templateContext = $this->templateContextBuilder->build($request, $dto);

        $this->messageBus->dispatch(new SendMortgageEmailMessage(
            emailTo: $request->email,
            templateContext: $templateContext,
        ));

        return new MortgageSendEmailResource(
            initialCostValue: $dto->initialCostValue,
            totalValue: $dto->totalValue,
            totalCostValue: $dto->totalCostValue,
            installmentValue: $dto->installmentValue,
        );
    }
}