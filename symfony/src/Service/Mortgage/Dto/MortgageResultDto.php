<?php

namespace App\Service\Mortgage\Dto;

use Brick\Math\BigDecimal;

final readonly class MortgageResultDto
{
    public function __construct(
        public BigDecimal $initialCostValue,
        public BigDecimal $totalValue,
        public BigDecimal $totalCostValue,
        public BigDecimal $installmentValue,
    ) {
    }
}
