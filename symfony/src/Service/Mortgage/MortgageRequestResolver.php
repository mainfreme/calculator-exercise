<?php

namespace App\Service\Mortgage;

use ApiPlatform\Exception\InvalidUriVariableException;
use App\ApiRequest\MortgageRequest;
use App\ApiRequest\MortgageSendEmailRequest;
use App\ApiRequest\MortgageV2Request;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Validator\ValidatorInterface;
use App\Service\Mortgage\Dto\MortgageCalculationResultDto;
use App\Service\Mortgage\Dto\MortgageResolutionResult;
use App\Service\Mortgage\MortgageCalculator;

class MortgageRequestResolver
{

    public function __construct(
        public ValidatorInterface $validation,
        public MortgageCalculator $mortgageCalculator,
    ) {}

    public function resolve(Operation $operation, array $context): MortgageResolutionResult
    {
        $request = $this->createRequest($operation, $context);
        $this->validation->validate($request);

        $calculationResult = $this->mortgageCalculator->calculate(
            creditValue: $request->creditValue,
            period: $request->period,
            margin: $request->margin,
            provision: $request->provision
        );

        return new MortgageResolutionResult($request, $calculationResult);
    }

    /**
     * @throws InvalidUriVariableException
     */
    public function createRequest(Operation $operation, array $context): MortgageRequest|MortgageV2Request|MortgageSendEmailRequest
    {
        $filters = $context['filters'] ?? [];
        if (empty($filters) && isset($context['request']) && $context['request'] instanceof \Symfony\Component\HttpFoundation\Request) {
            $httpRequest = $context['request'];
            $filters = array_merge(
                $httpRequest->query->all(),
                $httpRequest->request->all()
            );
        }
        $input = $operation->getInput();
        $inputClass = \is_array($input) ? ($input['class'] ?? null) : null;

        try {
            return match ($inputClass) {
                MortgageV2Request::class => new MortgageV2Request(...$filters),
                MortgageSendEmailRequest::class => new MortgageSendEmailRequest(...$filters),
                default => new MortgageRequest(...$filters),
            };
        } catch (\InvalidArgumentException $exception) {
            throw new InvalidUriVariableException();
        }
    }
}