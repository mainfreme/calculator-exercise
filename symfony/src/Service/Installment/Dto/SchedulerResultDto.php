<?php

namespace App\Service\Installment\Dto;

class SchedulerResultDto
{
    public function __construct(
        public readonly ?int $period = null,
        public readonly ?float $capital = null,
        public readonly ?float $interest = null,
        public readonly ?float $total = null
    ){}
}
