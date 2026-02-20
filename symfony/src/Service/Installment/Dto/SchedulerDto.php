<?php

namespace App\Service\Installment\Dto;

class SchedulerDto
{
    public function __construct(
        public readonly ?int $creditValue = null,
        public readonly ?float $provision = null,
        public readonly ?float $margin = null,
        public readonly ?int $period = null,
        public readonly bool $fullSchedule = false,
    ) {
    }
}
