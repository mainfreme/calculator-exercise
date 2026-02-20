<?php

namespace App\Tests\Service\Installment;

use App\Service\Installment\Dto\InstallmentDto;
use App\Service\Installment\DummyInstallment;
use PHPUnit\Framework\TestCase;

class DummyInstallmentTest extends TestCase
{
    private DummyInstallment $installment;

    protected function setUp(): void
    {
        $this->installment = new DummyInstallment();
    }

    public function testCalculateReturnsCorrectValuesFor210000(): void
    {
        $dto = new InstallmentDto(
            creditValue: 210_000.0,
            period: 240,
            margin: 2.5,
            provision: 1.0,
        );

        $result = $this->installment->calculate($dto);

        self::assertEqualsWithDelta(437.5, $result->interestValue ?? 0, 0.01);
        self::assertEqualsWithDelta(675.3, $result->capitalValue ?? 0, 0.01);
        self::assertEqualsWithDelta(1112.8, $result->totalValue ?? 0, 0.01);
    }

    public function testCalculateCapitalPlusInterestEqualsTotal(): void
    {
        $dto = new InstallmentDto(
            creditValue: 400_000.0,
            period: 240,
            margin: 2.5,
            provision: 1.0,
        );

        $result = $this->installment->calculate($dto);

        $sum = ($result->capitalValue ?? 0) + ($result->interestValue ?? 0);
        self::assertEqualsWithDelta(
            $result->totalValue ?? 0,
            $sum,
            0.01,
            'capitalValue + interestValue should equal totalValue'
        );
    }

    public function testCalculateReturnsCorrectValuesFor400000(): void
    {
        $dto = new InstallmentDto(
            creditValue: 400_000.0,
            period: 240,
            margin: 2.5,
            provision: 1.0,
        );

        $result = $this->installment->calculate($dto);

        self::assertEqualsWithDelta(833.33, $result->interestValue ?? 0, 0.01);
        self::assertEqualsWithDelta(1286.28, $result->capitalValue ?? 0, 0.01);
        self::assertEqualsWithDelta(2119.61, $result->totalValue ?? 0, 0.01);
    }

    public function testCalculateWithShortPeriod(): void
    {
        $dto = new InstallmentDto(
            creditValue: 100_000.0,
            period: 12,
            margin: 5.0,
            provision: 2.0,
        );

        $result = $this->installment->calculate($dto);

        self::assertGreaterThan(0, $result->interestValue);
        self::assertGreaterThan(0, $result->capitalValue);
        self::assertGreaterThan(0, $result->totalValue);
        self::assertEqualsWithDelta(
            ($result->capitalValue ?? 0) + ($result->interestValue ?? 0),
            $result->totalValue ?? 0,
            0.01,
        );
    }
}
