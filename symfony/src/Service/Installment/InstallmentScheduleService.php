<?php

namespace App\Service\Installment;

use App\Service\Installment\Dto\SchedulerDto;
use MathPHP\Finance;

final class InstallmentScheduleService
{
    /**
     * @return array<int, array{month: int, interestValue: float, capitalValue: float, totalValue: float}>
     */
    public function generate(SchedulerDto $dto): array
    {
        $creditValue = (float) $dto->creditValue;
        $period = $dto->period;
        $monthlyRate = $dto->margin / 100 / 12;

        $monthsToInclude = $dto->fullSchedule
            ? range(1, $period)
            : $this->getMonthsToInclude($period);
        $result = [];

        foreach ($monthsToInclude as $month) {
            $interestValue = Finance::ipmt($monthlyRate, $month, $period, -$creditValue, 0, false);
            $capitalValue = Finance::ppmt($monthlyRate, $month, $period, -$creditValue, 0, false);
            $totalValue = Finance::pmt($monthlyRate, $period, -$creditValue, 0, false);

            $result[] = [
                'month' => $month,
                'interestValue' => round(abs($interestValue), 2),
                'capitalValue' => round(abs($capitalValue), 2),
                'totalValue' => round(abs($totalValue), 2),
            ];
        }

        return $result;
    }

    /**
     * @return int[]
     */
    private function getMonthsToInclude(int $period): array
    {
        $months = [];
        $firstYearCount = min(12, $period);

        for ($m = 1; $m <= $firstYearCount; ++$m) {
            $months[] = $m;
        }

        for ($yearStart = 13; $yearStart <= $period; $yearStart += 12) {
            $months[] = $yearStart;
        }

        return array_values(array_unique($months));
    }
}
