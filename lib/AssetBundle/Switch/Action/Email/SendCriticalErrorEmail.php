<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Email;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final class SendCriticalErrorEmail
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function __invoke(string $filename): void
    {
        $fromEmail = $_ENV['SWITCH_UPLOAD_FROM_MAIL'];
        $toEmail = $_ENV['SWITCH_UPLOAD_TO_MAIL'];
        $toUsername = $_ENV['SWITCH_UPLOAD_TO_USERNAME'];

        $email = (new TemplatedEmail())
            ->to(new Address(address: $toEmail, name: $toUsername))
            ->from($fromEmail)
            ->subject(subject: 'Upload Critical Error')
            ->htmlTemplate(template: '@FroqPortal/email/upload_error.html.twig')
            ->context([
                'filename' => $filename,
                'year' => date('Y'),
                'username' => $toUsername,
                'fromEmail' => $fromEmail,
                'date' => (new \DateTime())->format('F j, Y H:i'),
            ]);

        try {
            $this->mailer->send($email);
        } catch (\Exception $exception) {
            throw new \Exception(message: $exception->getMessage());
        }
    }
}
