<?php

namespace App\Service\Installment;

use App\Service\Installment\Dto\InstallmentDto;
use App\Service\Installment\Dto\InstallmentResultDto;
use MathPHP\Finance;

class DummyInstallment
{
    public function calculate(InstallmentDto $dto): InstallmentResultDto
    {
        $period = $dto->period;

        $monthlyRate = $dto->margin / 100 / 12;

        $interestValue = Finance::ipmt($monthlyRate, 1, $period, -$dto->creditValue, 0, false);
        $capitalValue = Finance::ppmt($monthlyRate, 1, $period, -$dto->creditValue, 0, false);
        $totalValue = Finance::pmt($monthlyRate, $period, -$dto->creditValue, 0, false);

        return new InstallmentResultDto(
            capitalValue: abs($capitalValue),
            interestValue: abs($interestValue),
            totalValue: abs($totalValue),
        );
    }
}
