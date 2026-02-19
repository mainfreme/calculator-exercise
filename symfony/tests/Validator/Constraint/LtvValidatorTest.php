<?php

namespace App\Tests\Validator\Constraint;

use App\ApiRequest\MortgageV2Request;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LtvValidatorTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidLtvPasses(): void
    {
        $request = new MortgageV2Request(
            creditValue: 400_000,
            secureValue: 500_000,
            period: 240,
            age: 35
        );

        $violations = $this->validator->validate($request);

        $ltvViolations = array_filter(
            iterator_to_array($violations),
            fn ($v) => 'creditValue' === $v->getPropertyPath() && str_contains($v->getMessage(), 'LTV')
        );

        $this->assertCount(0, $ltvViolations, 'LTV 80% (400k/500k) powinno być poprawne');
    }

    public function testLtvAtMaxLimitPasses(): void
    {
        $request = new MortgageV2Request(
            creditValue: 450_000,
            secureValue: 500_000,
            period: 240,
            age: 35
        );

        $violations = $this->validator->validate($request);

        $ltvViolations = array_filter(
            iterator_to_array($violations),
            fn ($v) => 'creditValue' === $v->getPropertyPath() && str_contains($v->getMessage(), 'LTV')
        );

        $this->assertCount(0, $ltvViolations, 'LTV 90% (450k/500k) - maksymalna dopuszczalna wartość');
    }

    public function testLtvExceedingMaxFails(): void
    {
        $request = new MortgageV2Request(
            creditValue: 500_000,
            secureValue: 500_000,
            period: 240,
            age: 35
        );

        $violations = $this->validator->validate($request);

        $ltvViolations = array_filter(
            iterator_to_array($violations),
            fn ($v) => 'creditValue' === $v->getPropertyPath() && str_contains($v->getMessage(), 'LTV')
        );

        $this->assertCount(1, $ltvViolations, 'LTV 100% (500k/500k) powinno zwrócić błąd walidacji');
        $this->assertStringContainsString('90%', (string) $ltvViolations[array_key_first($ltvViolations)]->getMessage());
    }

    public function testSkipsValidationWhenCreditValueIsNull(): void
    {
        $request = new MortgageV2Request(
            creditValue: null,
            secureValue: 500_000,
            period: 240,
            age: 35
        );

        $violations = $this->validator->validate($request);

        $ltvViolations = array_filter(
            iterator_to_array($violations),
            fn ($v) => str_contains($v->getMessage(), 'LTV')
        );

        $this->assertCount(0, $ltvViolations, 'Walidacja LTV nie powinna się wykonać gdy creditValue jest null');
    }

    public function testSkipsValidationWhenSecureValueIsNull(): void
    {
        $request = new MortgageV2Request(
            creditValue: 500_000,
            secureValue: null,
            period: 240,
            age: 35
        );

        $violations = $this->validator->validate($request);

        $ltvViolations = array_filter(
            iterator_to_array($violations),
            fn ($v) => str_contains($v->getMessage(), 'LTV')
        );

        $this->assertCount(0, $ltvViolations, 'Walidacja LTV nie powinna się wykonać gdy secureValue jest null');
    }
}
