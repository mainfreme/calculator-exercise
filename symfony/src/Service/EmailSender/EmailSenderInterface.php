<?php

namespace App\Service\EmailSender;

use App\Service\EmailSender\Dto\SendEmailDto;

interface EmailSenderInterface
{
    public function send(SendEmailDto $dto): void;
}
