<?php

namespace App\Service\Mortgage;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use MathPHP\Finance;
use App\Service\Mortgage\Dto\MortgageCalculationResultDto;

final class MortgageCalculator
{
    public function calculate(float $creditValue, int $period, float $margin, float $provision): MortgageCalculationResultDto
    {
        $installment = Finance::pmt($margin / 100 / 12, $period, -$creditValue, 0, false);

        return new MortgageCalculationResultDto(
            initialCost: BigDecimal::of($creditValue)
                ->multipliedBy($provision / 100)
                ->toScale(2, RoundingMode::HALF_UP),
            totalCost: BigDecimal::of($installment)
                ->multipliedBy($period)
                ->minus(BigDecimal::of($creditValue))
                ->toScale(2, RoundingMode::HALF_UP),
            installment: BigDecimal::of($installment)->toScale(2, RoundingMode::HALF_UP),
        );
    }
}
