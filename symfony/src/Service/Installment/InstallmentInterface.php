<?php

namespace App\Service\Installment;

use App\Service\Installment\Dto\SchedulerDto;
use App\Service\Installment\Dto\SchedulerResultDto;

interface InstallmentInterface
{
    public function calculate(): array;

    // @todo: to calculate capital part of installment use ppmt financial function
    public function scheduler(SchedulerDto $dto): array;
}