<?php

namespace App\Message;

final readonly class SendMortgageEmailMessage
{
    /**
     * @param array<string, mixed> $templateContext
     */
    public function __construct(
        public string $emailTo,
        public array $templateContext,
    ) {
    }
}
