<?php

namespace Cyve\PasswordManagerBundle\Tests\Mock;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;

class Mailer implements MailerInterface
{
    private array $messages = [];

    public function send(RawMessage $message, Envelope $envelope = null): void
    {
        $this->messages[] = $message;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
