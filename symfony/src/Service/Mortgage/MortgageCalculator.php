<?php

namespace App\Service\Mortgage;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use MathPHP\Finance;

final class MortgageCalculator
{
    public function calculate(float $creditValue, int $period, float $margin, float $provision)
    {
        $installment = Finance::pmt($margin / 100 / 12, $period, -$creditValue, 0, false);

        return [
            'initialCost' => BigDecimal::of($creditValue)
                ->multipliedBy($provision / 100)
                ->toScale(2, RoundingMode::HALF_UP),
            'totalCost' => BigDecimal::of($installment)
                ->multipliedBy($period)
                ->minus(BigDecimal::of($creditValue))
                ->toScale(2, RoundingMode::HALF_UP),
            'installment' => BigDecimal::of($installment)->toScale(2, RoundingMode::HALF_UP),
        ];
    }
}
