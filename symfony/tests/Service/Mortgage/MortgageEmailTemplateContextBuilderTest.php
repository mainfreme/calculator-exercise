<?php

namespace App\Tests\Service\Mortgage;

use App\ApiRequest\MortgageSendEmailRequest;
use App\Service\Installment\InstallmentScheduleService;
use App\Service\Mortgage\Dto\MortgageResultDto;
use App\Service\Mortgage\MortgageEmailTemplateContextBuilder;
use Brick\Math\BigDecimal;
use PHPUnit\Framework\TestCase;

class MortgageEmailTemplateContextBuilderTest extends TestCase
{
    private MortgageEmailTemplateContextBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new MortgageEmailTemplateContextBuilder(new InstallmentScheduleService());
    }

    public function testBuildReturnsCorrectTemplateContext(): void
    {
        $request = new MortgageSendEmailRequest(
            creditValue: 400_000,
            secureValue: 500_000,
            period: 240,
            age: 35,
            margin: 2.5,
            provision: 1.0,
            email: 'jan.kowalski@example.com',
        );

        $mortgageResult = new MortgageResultDto(
            initialCostValue: BigDecimal::of('4000.00'),
            totalValue: BigDecimal::of('471171.06'),
            totalCostValue: BigDecimal::of('71171.06'),
            installmentValue: BigDecimal::of('1112.80'),
        );

        $context = $this->builder->build($request, $mortgageResult);

        self::assertSame('Użytkowniku jan.kowalski', $context['name']);
        self::assertSame(400_000, $context['creditValue']);
        self::assertSame(500_000, $context['secureValue']);
        self::assertSame(2.5, $context['margin']);
        self::assertSame(1.0, $context['provision']);
        self::assertSame(240, $context['period']);
        self::assertSame(35, $context['age']);
        self::assertSame(1112.80, $context['installmentValue']);
        self::assertSame(4000.0, $context['initialCostValue']);
        self::assertSame(71171.06, $context['totalCostValue']);
        self::assertIsArray($context['array']);
        self::assertCount(240, $context['array']);
        self::assertArrayHasKey('month', $context['array'][0]);
        self::assertArrayHasKey('interestValue', $context['array'][0]);
        self::assertArrayHasKey('capitalValue', $context['array'][0]);
        self::assertArrayHasKey('totalValue', $context['array'][0]);
    }

    public function testBuildHandlesEmailWithoutAtSign(): void
    {
        $request = new MortgageSendEmailRequest(
            creditValue: 100_000,
            secureValue: 150_000,
            period: 12,
            age: 30,
            margin: 5.0,
            provision: 2.0,
            email: 'invalid-email',
        );

        $mortgageResult = new MortgageResultDto(
            initialCostValue: BigDecimal::of('2000.00'),
            totalValue: BigDecimal::of('103000.00'),
            totalCostValue: BigDecimal::of('3000.00'),
            installmentValue: BigDecimal::of('8566.12'),
        );

        $context = $this->builder->build($request, $mortgageResult);

        self::assertSame('Użytkowniku invalid-email', $context['name']);
    }
}
