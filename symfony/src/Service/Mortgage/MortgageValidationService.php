<?php

namespace App\Service\Mortgage;

final class MortgageValidationService
{
    public const MIN_CREDIT_VALUE = 100_000;
    public const MAX_CREDIT_VALUE = 10_000_000;
    public const MAX_LTV = 90;
    public const MIN_PERIOD_MONTHS = 12;
    public const MAX_PERIOD_MONTHS = 480; // 40 lat
    public const MAX_AGE_AT_LAST_INSTALLMENT = 70;

    public function validateLtv(int $creditValue, int $secureValue): ?string
    {
        if ($secureValue <= 0) {
            return null;
        }

        $ltv = ($creditValue / $secureValue) * 100;

        if ($ltv > self::MAX_LTV) {
            return sprintf('Poziom LTV nie może przekraczać %d%%. Obecna wartość: %.1f%%.', self::MAX_LTV, $ltv);
        }

        return null;
    }

    public function validateAgeAtLastInstallment(int $age, int $period): ?string
    {
        $ageAtLastInstallment = $age + (int) ($period / 12);

        if ($ageAtLastInstallment > self::MAX_AGE_AT_LAST_INSTALLMENT) {
            return sprintf(
                'Wiek kredytobiorcy w momencie spłaty ostatniej raty nie może przekraczać %d lat. Przy wieku %d lat i okresie %d miesięcy wiek wyniesie %d lat.',
                self::MAX_AGE_AT_LAST_INSTALLMENT,
                $age,
                $period,
                $ageAtLastInstallment
            );
        }

        return null;
    }
}
