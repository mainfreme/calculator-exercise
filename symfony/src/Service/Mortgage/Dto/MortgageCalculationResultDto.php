<?php

namespace App\Service\Mortgage\Dto;

use Brick\Math\BigDecimal;

class MortgageCalculationResultDto
{
    public function __construct(
        public readonly BigDecimal $initialCost,
        public readonly BigDecimal $totalCost,
        public readonly BigDecimal $installment,
    ) {
    }
}