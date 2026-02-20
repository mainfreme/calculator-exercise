<?php

namespace App\Service\Mortgage;

use App\ApiRequest\MortgageSendEmailRequest;
use App\Service\Installment\Dto\SchedulerDto;
use App\Service\Installment\InstallmentScheduleService;
use App\Service\Mortgage\Dto\MortgageResultDto;

class MortgageEmailTemplateContextBuilder
{
    public function __construct(
        private InstallmentScheduleService $installmentScheduleService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(
        MortgageSendEmailRequest $request,
        MortgageResultDto $mortgageResult,
    ): array {
        $schedule = $this->installmentScheduleService->generate(
            new SchedulerDto(
                creditValue: $request->creditValue,
                margin: $request->margin,
                period: $request->period,
                fullSchedule: true,
            )
        );

        $user_name = @explode('@', $request->email)[0] ?? '';

        return [
            'name' => 'Użytkowniku ' . $user_name,
            'creditValue' => $request->creditValue,
            'secureValue' => $request->secureValue,
            'margin' => $request->margin,
            'provision' => $request->provision,
            'period' => $request->period,
            'age' => $request->age,
            'installmentValue' => $mortgageResult->installmentValue->toFloat(),
            'initialCostValue' => $mortgageResult->initialCostValue->toFloat(),
            'totalCostValue' => $mortgageResult->totalCostValue->toFloat(),
            'array' => $schedule,
        ];
    }
}
