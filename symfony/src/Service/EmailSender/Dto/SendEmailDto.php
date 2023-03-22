<?php

namespace App\Service\EmailSender\Dto;

class SendEmailDto
{
    public $emailTo;
    public $emailFrom;
    public $subject;
    public $content;
}