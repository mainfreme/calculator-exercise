<?php

namespace App\Api\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;

class MortgageTest extends ApiTestCase
{
    protected static function createClient(array $kernelOptions = [], array $defaultOptions = []): Client
    {
        return parent::createClient([], ['headers' => ['Accept' => 'application/json']]);
    }

    /**
     * @dataProvider provideMortgageData
     */
    public function testGetMortgageSuccessfully(array $filters, array $results)
    {
        static::createClient()->request('GET', '/api/mortgage?'.http_build_query($filters));

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains($results);
    }

    /**
     * @dataProvider provideMortgageMissingFiltersData
     */
    public function testGetMortgageWhenMissingFilterFailure(array $filters, array $results)
    {
        static::createClient()->request('GET', '/api/mortgage?'.http_build_query($filters));
        $this->assertResponseIsUnprocessable();
        $this->assertJsonContains(['violations' => [
            $results,
        ]], false);
    }

    public static function provideMortgageData(): array
    {
        return [
            [
                [
                    'creditValue' => 210000,
                    'secureValue' => 400000,
                    'age' => 35,
                    'provision' => 1,
                    'margin' => 2.5,
                    'period' => 240,
                ],
                [
                    'initialCostValue' => '2100.00',
                    'totalCostValue' => '59171.06',
                    'installmentValue' => '1112.80',
                    'totalValue' => '269171.06',
                ],
            ],
        ];
    }

    public static function provideMortgageMissingFiltersData(): array
    {
        return [
            [
                [
                    'secureValue' => 400000,
                    'age' => 35,
                    'provision' => 1,
                    'margin' => 2.5,
                    'period' => 240,
                ],
                [
                    'propertyPath' => 'creditValue',
                    'message' => 'This value should not be blank.',
                ],
            ],
            [
                [
                    'creditValue' => 400000,
                    'age' => 35,
                    'provision' => 1,
                    'margin' => 2.5,
                    'period' => 240,
                ],
                [
                    'propertyPath' => 'secureValue',
                    'message' => 'This value should not be blank.',
                ],
            ],
            [
                [
                    'secureValue' => 400000,
                    'creditValue' => 350000,
                    'provision' => 1,
                    'margin' => 2.5,
                    'period' => 240,
                ],
                [
                    'propertyPath' => 'age',
                    'message' => 'This value should not be blank.',
                ],
            ],
            [
                [
                    'secureValue' => 400000,
                    'age' => 35,
                    'creditValue' => 300000,
                    'margin' => 2.5,
                    'period' => 240,
                ],
                [
                    'propertyPath' => 'provision',
                    'message' => 'This value should not be blank.',
                ],
            ],
            [
                [
                    'secureValue' => 400000,
                    'age' => 35,
                    'provision' => 1,
                    'creditValue' => 250000,
                    'period' => 240,
                ],
                [
                    'propertyPath' => 'margin',
                    'message' => 'This value should not be blank.',
                ],
            ],
            [
                [
                    'secureValue' => 400000,
                    'age' => 35,
                    'provision' => 1,
                    'margin' => 2.5,
                    'creditValue' => 240000,
                ],
                [
                    'propertyPath' => 'period',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ];
    }
}
