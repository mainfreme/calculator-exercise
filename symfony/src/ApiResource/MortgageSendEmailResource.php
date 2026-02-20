<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use ApiPlatform\OpenApi\Model\Response as OpenApiResponse;
use App\ApiProvider\MortageEmailProvider;
use App\ApiRequest\MortgageSendEmailRequest;
use App\Service\Mortgage\MortgageValidationService;
use Brick\Math\BigDecimal;
use ApiPlatform\Metadata\Post;


#[ApiResource(
    shortName: 'MortgageSendEmail',
    routePrefix: '/v2/',
    operations: [
        new Post(
            uriTemplate: 'mortgage/send-email.{_format}',
            status: 202,
            description: 'Mortgage Send email',
            input: MortgageSendEmailRequest::class,
            output: MortgageV2Resource::class,
            provider: MortageEmailProvider::class,
            exceptionToStatus: [
                \ApiPlatform\Validator\Exception\ValidationException::class => 422,
            ],
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
                    new OpenApiParameter(
                        'email',
                        'query',
                        'Email',
                        true,
                        false,
                        false,
                        ['type' => 'string']
                    ),
                ],
                responses: [
                    '422' => new OpenApiResponse(
                        'Niezgodność danych wejściowych z regułami walidacji. Zwraca listę błędów.'
                    ),
                ]
            )
        ),
    ],
    formats: ['json']
)]
class MortgageSendEmailResource
{
    public function __construct(
        public readonly ?BigDecimal $initialCostValue = null,
        public readonly ?BigDecimal $totalValue = null,
        public readonly ?BigDecimal $totalCostValue = null,
        public readonly ?BigDecimal $installmentValue = null
    ) {
    }
}