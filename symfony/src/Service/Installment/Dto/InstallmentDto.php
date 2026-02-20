<?php

namespace App\Service\Installment\Dto;

final readonly class InstallmentDto
{
    public function __construct(
        public readonly float $creditValue,
        public readonly int $period,
        public readonly float $margin,
        public readonly float $provision,
    ) {
    }
}