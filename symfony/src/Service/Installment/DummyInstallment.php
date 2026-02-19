<?php

namespace App\Service\Installment;

use MathPHP\Finance;

class DummyInstallment
{
    public function calculate($creditValue, $period, $margin, $provision): array
    {
        $interest = $creditValue / $period / $margin / 100;
        $installment = Finance::pmt($margin / 100 / 12, $period, $creditValue, 0, false);

        return [
            'capitalValue' => $creditValue / $period / $margin / 100,
            'interestValue' => $creditValue / $period / $margin,
            'totalValue' => abs($installment),
        ];
    }
}
