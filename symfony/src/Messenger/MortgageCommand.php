<?php

namespace App\Messenger;

class MortgageCommand
{
    public function __construct(
        public readonly ?int $creditValue = null,
        public readonly ?int $secureValue = null,
        public readonly ?int $period = null,
        public readonly ?int $age = null
    ) {
    }
}
