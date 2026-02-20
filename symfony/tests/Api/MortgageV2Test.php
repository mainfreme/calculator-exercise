<?php

namespace App\Api\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;

class MortgageV2Test extends ApiTestCase
{
    private const VALID_PARAMS = [
        'creditValue' => 210000,
        'secureValue' => 400000,
        'period' => 240,
        'age' => 35,
        'margin' => 2.5,
        'provision' => 1,
    ];

    protected static function createClient(array $kernelOptions = [], array $defaultOptions = []): Client
    {
        return parent::createClient([], ['headers' => ['Accept' => 'application/json']]);
    }

    public function testValidRequestReturns200(): void
    {
        $response = static::createClient()->request('GET', '/api/v2/mortgage?'.http_build_query(self::VALID_PARAMS));

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'initialCostValue' => '2100.00',
            'totalCostValue' => '59171.06',
            'installmentValue' => '1112.80',
            'totalValue' => '269171.06',
        ]);
    }

    /**
     * @dataProvider provideValidationEdgeCases
     */
    public function testValidationEdgeCasesReturn422(array $params): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/v2/mortgage?'.http_build_query($params));

        $this->assertResponseStatusCodeSame(422);
        $content = $client->getKernelBrowser()->getResponse()->getContent();
        $data = json_decode($content, true);
        $this->assertNotEmpty($data['violations'] ?? []);
    }

    public static function provideValidationEdgeCases(): array
    {
        $valid = self::VALID_PARAMS;

        return [
            'creditValue brak' => [array_diff_key($valid, ['creditValue' => 1])],
            'creditValue poniżej min' => [['creditValue' => 99999] + $valid],
            'creditValue powyżej max' => [['creditValue' => 10000001, 'secureValue' => 12000000] + $valid],
            'secureValue brak' => [array_diff_key($valid, ['secureValue' => 1])],
            'LTV powyżej 90%' => [['creditValue' => 455000, 'secureValue' => 500000] + $valid],
            'period brak' => [array_diff_key($valid, ['period' => 1])],
            'period poniżej min' => [['period' => 11] + $valid],
            'period powyżej max' => [['period' => 481, 'age' => 30] + $valid],
            'age brak' => [array_diff_key($valid, ['age' => 1])],
            'wiek+okres powyżej 70' => [['age' => 70, 'period' => 12] + $valid],
            'margin brak' => [array_diff_key($valid, ['margin' => 1])],
            'provision brak' => [array_diff_key($valid, ['provision' => 1])],
        ];
    }
}
