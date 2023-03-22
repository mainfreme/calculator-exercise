<?php

namespace App\Service\Installment;

use App\Service\Installment\Dto\SchedulerDto;
use App\Service\Installment\Dto\SchedulerResultDto;

interface InstallmentInterface
{
    public function calculate(): array;

    public function scheduler(SchedulerDto $dto): array;
}