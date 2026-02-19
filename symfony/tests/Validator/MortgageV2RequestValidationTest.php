<?php

namespace App\Tests\Validator;

use App\ApiRequest\MortgageV2Request;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MortgageV2RequestValidationTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    private const DEFAULTS = [
        'creditValue' => 400_000,
        'secureValue' => 500_000,
        'period' => 240,
        'age' => 35,
        'margin' => 2.5,
        'provision' => 1.0,
    ];

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidRequestPasses(): void
    {
        $request = new MortgageV2Request(...self::DEFAULTS);
        $this->assertCount(0, $this->validator->validate($request));
    }

    /**
     * @dataProvider provideValidationEdgeCases
     */
    public function testValidationEdgeCases(array $overrides, bool $shouldPass): void
    {
        $request = new MortgageV2Request(...array_merge(self::DEFAULTS, $overrides));
        $violations = $this->validator->validate($request);

        if ($shouldPass) {
            $this->assertCount(0, $violations);
        } else {
            $this->assertGreaterThan(0, $violations->count());
        }
    }

    public static function provideValidationEdgeCases(): array
    {
        return [
            'creditValue null' => [['creditValue' => null], false],
            'creditValue poniżej min' => [['creditValue' => 99_999], false],
            'creditValue na min' => [['creditValue' => 100_000], true],
            'creditValue na max' => [['creditValue' => 10_000_000, 'secureValue' => 12_000_000], true],
            'creditValue powyżej max' => [['creditValue' => 10_000_001, 'secureValue' => 12_000_000], false],
            'secureValue null' => [['secureValue' => null], false],
            'LTV 90% OK' => [['creditValue' => 450_000, 'secureValue' => 500_000], true],
            'LTV powyżej 90%' => [['creditValue' => 455_000, 'secureValue' => 500_000], false],
            'period null' => [['period' => null], false],
            'period poniżej min' => [['period' => 11], false],
            'period na min' => [['period' => 12], true],
            'period na max' => [['period' => 480, 'age' => 30], true],
            'period powyżej max' => [['period' => 481, 'age' => 30], false],
            'age null' => [['age' => null], false],
            'wiek+okres na limicie 70' => [['age' => 65, 'period' => 60], true],
            'wiek+okres powyżej 70' => [['age' => 70, 'period' => 12], false],
            'margin null' => [['margin' => null], false],
            'provision null' => [['provision' => null], false],
        ];
    }
}
