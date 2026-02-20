<?php

namespace App\ApiRequest;

use App\Service\Mortgage\MortgageValidationService;
use App\Validator\Constraint\AgeAtLastInstallment;
use App\Validator\Constraint\Ltv;
use Symfony\Component\Validator\Constraints as Assert;

#[AgeAtLastInstallment]
class MortgageSendEmailRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Ltv]
        #[Assert\Range(
            min: MortgageValidationService::MIN_CREDIT_VALUE,
            max: MortgageValidationService::MAX_CREDIT_VALUE,
            notInRangeMessage: 'Kwota kredytu musi być między {{ min }} a {{ max }} PLN.'
        )]
        public readonly ?int $creditValue = null,
        #[Assert\NotBlank]
        public readonly ?int $secureValue = null,
        #[Assert\NotBlank]
        #[Assert\Range(
            min: MortgageValidationService::MIN_PERIOD_MONTHS,
            max: MortgageValidationService::MAX_PERIOD_MONTHS,
            notInRangeMessage: 'Okres kredytowania musi być między {{ min }} a {{ max }} miesięcy.'
        )]
        public readonly ?int $period = null,
        #[Assert\NotBlank]
        public readonly ?int $age = null,
        #[Assert\NotBlank]
        public readonly ?float $margin = null,
        #[Assert\NotBlank]
        public readonly ?float $provision = null,
        #[Assert\NotBlank]
        #[Assert\Email]
        public readonly ?string $email = null,
    ) {
    }
}
