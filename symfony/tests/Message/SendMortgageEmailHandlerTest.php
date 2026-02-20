<?php

namespace App\Tests\Message;

use App\Message\SendMortgageEmailHandler;
use App\Message\SendMortgageEmailMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;

class SendMortgageEmailHandlerTest extends TestCase
{
    private MailerInterface $mailer;

    private LoggerInterface $logger;

    private SendMortgageEmailHandler $handler;

    protected function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->handler = new SendMortgageEmailHandler($this->mailer, $this->logger);
    }

    public function testInvokeSendsEmailSuccessfully(): void
    {
        $message = new SendMortgageEmailMessage(
            emailTo: 'user@example.com',
            templateContext: [
                'creditValue' => 400000,
                'installmentValue' => 1112.80,
            ],
        );

        $this->mailer->expects(self::once())
            ->method('send')
            ->with(self::callback(static function ($email) {
                return $email->getTo()[0]->getAddress() === 'user@example.com'
                    && $email->getSubject() === 'Szczegóły kredytu hipotecznego';
            }));

        $this->logger->expects(self::once())
            ->method('info')
            ->with('Mortgage email sent successfully', ['to' => 'user@example.com']);

        ($this->handler)($message);
    }

    public function testInvokeThrowsRecoverableExceptionOnTransportFailure(): void
    {
        $message = new SendMortgageEmailMessage(
            emailTo: 'user@example.com',
            templateContext: [],
        );

        $transportException = new TransportException('SMTP timeout');

        $this->mailer->expects(self::once())
            ->method('send')
            ->willThrowException($transportException);

        $this->logger->expects(self::once())
            ->method('error')
            ->with(
                'Failed to send mortgage email',
                self::callback(static function (array $context) use ($transportException) {
                    return $context['to'] === 'user@example.com'
                        && $context['error'] === 'SMTP timeout'
                        && $context['exception'] === $transportException;
                })
            );

        $this->expectException(RecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('Email transport failed: SMTP timeout');

        ($this->handler)($message);
    }
}
