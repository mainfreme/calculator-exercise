<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Controller\MortgageCalculator;
use App\ApiRequest\MortgageRequest;
use App\Messenger\MortgageCommand;
use App\ApiProvider\MortgageProvider;
use Brick\Math\BigDecimal;

#[ApiResource(
    shortName: 'Mortgage',
    operations: [
        new Get(
            uriTemplate: 'mortgage.{_format}',
            status: 200,
            description: 'Mortgage Calculator',
            input: MortgageRequest::class,
            output: MortgageResource::class,
            provider: MortgageProvider::class
        )
    ],
    formats: ['json']
)]
class MortgageResource
{
    public function __construct(
        public readonly ?BigDecimal $initialCostValue = null,
        public readonly ?BigDecimal $totalValue = null,
        public readonly ?BigDecimal $totalCostValue = null,
        public readonly ?BigDecimal $installmentValue = null
    )
    {}
}
