<?php

namespace App\Service\Mortgage\Dto;

use App\ApiRequest\MortgageRequest;
use App\ApiRequest\MortgageSendEmailRequest;
use App\ApiRequest\MortgageV2Request;

class MortgageResolutionResult
{
    public function __construct(
        public readonly MortgageRequest|MortgageV2Request|MortgageSendEmailRequest $request,
        public readonly MortgageCalculationResultDto $calculationResult,
    ) {
    }
}
