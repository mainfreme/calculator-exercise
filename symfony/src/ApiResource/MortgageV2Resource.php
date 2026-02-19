<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use App\ApiRequest\MortgageV2Request;
use App\ApiProvider\MortgageProvider;
use App\Service\Mortgage\MortgageValidationService;
use Brick\Math\BigDecimal;

#[ApiResource(
    shortName: 'MortgageV2',
    routePrefix: '/v2/',
    operations: [
        new Get(
            uriTemplate: 'mortgage.{_format}',
            status: 200,
            description: 'Mortgage Calculator',
            input: MortgageV2Request::class,
            output: MortgageV2Resource::class,
            provider: MortgageProvider::class,
            openapi: new OpenApiOperation(
                parameters: [
                    new OpenApiParameter(
                        'creditValue',
                        'query',
                        'Kwota kredytu (PLN)',
                        true,
                        false,
                        false,
                        [
                            'type' => 'integer',
                            'minimum' => MortgageValidationService::MIN_CREDIT_VALUE,
                            'maximum' => MortgageValidationService::MAX_CREDIT_VALUE,
                        ]
                    ),
                    new OpenApiParameter(
                        'secureValue',
                        'query',
                        'Wartość zabezpieczenia (PLN)',
                        true,
                        false,
                        false,
                        ['type' => 'integer']
                    ),
                    new OpenApiParameter(
                        'period',
                        'query',
                        'Okres kredytowania (miesiące)',
                        true,
                        false,
                        false,
                        [
                            'type' => 'integer',
                            'minimum' => MortgageValidationService::MIN_PERIOD_MONTHS,
                            'maximum' => MortgageValidationService::MAX_PERIOD_MONTHS,
                        ]
                    ),
                    new OpenApiParameter(
                        'age',
                        'query',
                        'Wiek kredytobiorcy (lata)',
                        true,
                        false,
                        false,
                        ['type' => 'integer']
                    ),
                    new OpenApiParameter(
                        'margin',
                        'query',
                        'Marża kredytowa (%)',
                        true,
                        false,
                        false,
                        ['type' => 'number', 'format' => 'float']
                    ),
                    new OpenApiParameter(
                        'provision',
                        'query',
                        'Prowizja (%)',
                        true,
                        false,
                        false,
                        ['type' => 'number', 'format' => 'float']
                    ),
                ]
            )
        )
    ],
    formats: ['json']
)]
class MortgageV2Resource
{
    public function __construct(
        public readonly ?BigDecimal $initialCostValue = null,
        public readonly ?BigDecimal $totalValue = null,
        public readonly ?BigDecimal $totalCostValue = null,
        public readonly ?BigDecimal $installmentValue = null
    )
    {}
}
