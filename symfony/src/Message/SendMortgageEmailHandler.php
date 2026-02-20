<?php

namespace App\Message;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;

#[AsMessageHandler]
final class SendMortgageEmailHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(SendMortgageEmailMessage $message): void
    {
        try {
            $email = (new TemplatedEmail())
                ->subject('Szczegóły kredytu hipotecznego')
                ->to($message->emailTo)
                ->from('mail@speedfin.pl')
                ->sender('mail@speedfin.pl')
                ->htmlTemplate('email/mortgage.html.twig')
                ->context($message->templateContext);

            $this->mailer->send($email);
            $this->logger->info('Mortgage email sent successfully', ['to' => $message->emailTo]);
        } catch (TransportExceptionInterface $exception) {
            $this->logger->error('Failed to send mortgage email', [
                'to' => $message->emailTo,
                'error' => $exception->getMessage(),
                'exception' => $exception,
            ]);

            throw new RecoverableMessageHandlingException(
                'Email transport failed: ' . $exception->getMessage(),
                0,
                $exception
            );
        }
    }
}
