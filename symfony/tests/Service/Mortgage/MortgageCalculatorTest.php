<?php

namespace App\Tests\Service\Mortgage;

use App\Service\Mortgage\MortgageCalculator;
use PHPUnit\Framework\TestCase;

class MortgageCalculatorTest extends TestCase
{
    private MortgageCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new MortgageCalculator();
    }

    public function testCalculateReturnsCorrectInitialCost(): void
    {
        $result = $this->calculator->calculate(
            creditValue: 210_000.0,
            period: 240,
            margin: 2.5,
            provision: 1.0,
        );

        self::assertEquals('2100.00', $result->initialCost->toScale(2)->__toString());
    }

    public function testCalculateReturnsCorrectInstallmentAndTotalCost(): void
    {
        $result = $this->calculator->calculate(
            creditValue: 210_000.0,
            period: 240,
            margin: 2.5,
            provision: 1.0,
        );

        self::assertEquals('1112.80', $result->installment->toScale(2)->__toString());
        self::assertEquals('57071.06', $result->totalCost->toScale(2)->__toString());
    }

    public function testCalculateInitialCostFormula(): void
    {
        $result = $this->calculator->calculate(
            creditValue: 100_000.0,
            period: 12,
            margin: 5.0,
            provision: 2.0,
        );

        self::assertEquals('2000.00', $result->initialCost->toScale(2)->__toString());
    }

    public function testCalculateTotalCostEqualsInstallmentTimesPeriodMinusCredit(): void
    {
        $creditValue = 400_000.0;
        $period = 240;
        $result = $this->calculator->calculate(
            creditValue: $creditValue,
            period: $period,
            margin: 2.5,
            provision: 1.0,
        );

        $totalPaid = $result->installment->toFloat() * $period;
        $expectedTotalCost = round($totalPaid - $creditValue, 2);

        self::assertEqualsWithDelta(
            $expectedTotalCost,
            $result->totalCost->toFloat(),
            1.0,
            'totalCost ≈ (installment × period) - creditValue (delta 1.0 dla zaokrągleń przy 240 ratach)'
        );
    }

    public function testCalculateRoundsToTwoDecimalPlaces(): void
    {
        $result = $this->calculator->calculate(
            creditValue: 123_456.0,
            period: 36,
            margin: 3.33,
            provision: 1.5,
        );

        self::assertMatchesRegularExpression('/^\d+\.\d{2}$/', $result->initialCost->toScale(2)->__toString());
        self::assertMatchesRegularExpression('/^\d+\.\d{2}$/', $result->totalCost->toScale(2)->__toString());
        self::assertMatchesRegularExpression('/^\d+\.\d{2}$/', $result->installment->toScale(2)->__toString());
    }
}
