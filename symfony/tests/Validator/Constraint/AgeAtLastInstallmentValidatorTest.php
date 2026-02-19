<?php

namespace App\Tests\Validator\Constraint;

use App\ApiRequest\MortgageV2Request;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AgeAtLastInstallmentValidatorTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidAgeAtLastInstallmentPasses(): void
    {
        $request = new MortgageV2Request(
            creditValue: 400_000,
            secureValue: 500_000,
            period: 240,
            age: 35
        );

        $violations = $this->validator->validate($request);

        $ageViolations = array_filter(
            iterator_to_array($violations),
            fn ($v) => $v->getPropertyPath() === 'period' && str_contains($v->getMessage(), 'Wiek kredytobiorcy')
        );

        $this->assertCount(0, $ageViolations, 'Wiek 35 + 20 lat = 55 lat - poniżej limitu 70');
    }

    public function testAgeAtMaxLimitPasses(): void
    {
        $request = new MortgageV2Request(
            creditValue: 400_000,
            secureValue: 500_000,
            period: 60,
            age: 65
        );

        $violations = $this->validator->validate($request);

        $ageViolations = array_filter(
            iterator_to_array($violations),
            fn ($v) => $v->getPropertyPath() === 'period' && str_contains($v->getMessage(), 'Wiek kredytobiorcy')
        );

        $this->assertCount(0, $ageViolations, 'Wiek 65 + 5 lat = 70 lat - dokładnie na limicie');
    }

    public function testAgeExceedingMaxFails(): void
    {
        $request = new MortgageV2Request(
            creditValue: 400_000,
            secureValue: 500_000,
            period: 120,
            age: 65
        );

        $violations = $this->validator->validate($request);

        $ageViolations = array_filter(
            iterator_to_array($violations),
            fn ($v) => $v->getPropertyPath() === 'period' && str_contains($v->getMessage(), 'Wiek kredytobiorcy')
        );

        $this->assertCount(1, $ageViolations, 'Wiek 65 + 10 lat = 75 lat - powyżej limitu 70');
        $this->assertStringContainsString('70 lat', (string) $ageViolations[array_key_first($ageViolations)]->getMessage());
    }

    public function testSkipsValidationWhenAgeIsNull(): void
    {
        $request = new MortgageV2Request(
            creditValue: 400_000,
            secureValue: 500_000,
            period: 240,
            age: null
        );

        $violations = $this->validator->validate($request);

        $ageViolations = array_filter(
            iterator_to_array($violations),
            fn ($v) => str_contains($v->getMessage(), 'Wiek kredytobiorcy')
        );

        $this->assertCount(0, $ageViolations, 'Walidacja nie powinna się wykonać gdy age jest null');
    }

    public function testSkipsValidationWhenPeriodIsNull(): void
    {
        $request = new MortgageV2Request(
            creditValue: 400_000,
            secureValue: 500_000,
            period: null,
            age: 35
        );

        $violations = $this->validator->validate($request);

        $ageViolations = array_filter(
            iterator_to_array($violations),
            fn ($v) => str_contains($v->getMessage(), 'Wiek kredytobiorcy')
        );

        $this->assertCount(0, $ageViolations, 'Walidacja nie powinna się wykonać gdy period jest null');
    }
}
