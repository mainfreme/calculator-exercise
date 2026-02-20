<?php

namespace App\Tests\Service\Mortgage;

use App\ApiRequest\MortgageV2Request;
use App\Service\Installment\Dto\InstallmentResultDto;
use App\Service\Mortgage\Dto\MortgageCalculationResultDto;
use App\Service\Mortgage\Dto\MortgageResolutionResult;
use App\Service\Mortgage\MortgageResourceAssembler;
use Brick\Math\BigDecimal;
use PHPUnit\Framework\TestCase;

class MortgageResourceAssemblerTest extends TestCase
{
    private MortgageResourceAssembler $assembler;

    protected function setUp(): void
    {
        $this->assembler = new MortgageResourceAssembler();
    }

    public function testAssembleReturnsCorrectMortgageResultDto(): void
    {
        $request = new MortgageV2Request(
            creditValue: 400_000,
            secureValue: 500_000,
            period: 240,
            age: 35,
            margin: 2.5,
            provision: 1.0,
        );

        $calculationResult = new MortgageCalculationResultDto(
            initialCost: BigDecimal::of('4000.00'),
            totalCost: BigDecimal::of('67171.06'),
            installment: BigDecimal::of('1112.80'),
        );

        $resolution = new MortgageResolutionResult($request, $calculationResult);

        $installment = new InstallmentResultDto(
            capitalValue: 500.00,
            interestValue: 612.80,
            totalValue: 1112.80,
        );

        $result = $this->assembler->assemble($resolution, $installment);

        self::assertEquals('4000.00', $result->initialCostValue->toScale(2)->__toString());
        self::assertEquals('471171.06', $result->totalValue->toScale(2)->__toString());
        self::assertEquals('71171.06', $result->totalCostValue->toScale(2)->__toString());
        self::assertEquals('1112.80', $result->installmentValue->toScale(2)->__toString());
    }

    public function testAssembleRoundsCorrectly(): void
    {
        $request = new MortgageV2Request(
            creditValue: 100_000,
            secureValue: 150_000,
            period: 12,
            age: 30,
            margin: 5.0,
            provision: 2.0,
        );

        $calculationResult = new MortgageCalculationResultDto(
            initialCost: BigDecimal::of('2000.005'),
            totalCost: BigDecimal::of('1234.567'),
            installment: BigDecimal::of('8566.123'),
        );

        $resolution = new MortgageResolutionResult($request, $calculationResult);

        $installment = new InstallmentResultDto(
            capitalValue: 8000.123,
            interestValue: 566.123,
            totalValue: 8566.123,
        );

        $result = $this->assembler->assemble($resolution, $installment);

        self::assertEquals('2000.01', $result->initialCostValue->toScale(2)->__toString());
        self::assertEquals('103234.57', $result->totalValue->toScale(2)->__toString());
        self::assertEquals('3234.57', $result->totalCostValue->toScale(2)->__toString());
        self::assertEquals('8566.12', $result->installmentValue->toScale(2)->__toString());
    }
}
