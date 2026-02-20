<?php

namespace App\Service\Mortgage;

use App\Service\Installment\Dto\InstallmentResultDto;
use App\Service\Mortgage\Dto\MortgageResolutionResult;
use App\Service\Mortgage\Dto\MortgageResultDto;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class MortgageResourceAssembler
{
    public function assemble(MortgageResolutionResult $resolution, InstallmentResultDto $installment): MortgageResultDto
    {
        $request = $resolution->request;
        $result = $resolution->calculationResult;

        return new MortgageResultDto(
            initialCostValue: BigDecimal::of($result->initialCost)->toScale(2, RoundingMode::HALF_UP),
            totalValue: BigDecimal::of($request->creditValue)
                ->plus($result->initialCost)
                ->plus($result->totalCost)
                ->toScale(2, RoundingMode::HALF_UP),
            totalCostValue: BigDecimal::of($result->totalCost)
                ->plus($result->initialCost)
                ->toScale(2, RoundingMode::HALF_UP),
            installmentValue: BigDecimal::of($installment->totalValue)->toScale(2, RoundingMode::HALF_UP),
        );
    }
}
