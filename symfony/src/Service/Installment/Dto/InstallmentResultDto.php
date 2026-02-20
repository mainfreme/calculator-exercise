<?php

namespace App\Service\Installment\Dto;

final readonly class InstallmentResultDto
{
    public function __construct(
        public readonly ?float $capitalValue,
        public readonly ?float $interestValue,
        public readonly ?float $totalValue,
    ) {
    }
}