<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Tests\Mailtrap;

use Vajexal\AmpMailer\Address;

class Message
{
    private int     $id;
    private int     $inboxId;
    private Address $from;
    private Address $to;
    private string  $subject;

    public function __construct(array $data)
    {
        $this->id      = $data['id'];
        $this->inboxId = $data['inbox_id'];
        $this->from    = new Address($data['from_email'], $data['from_name']);
        $this->to      = new Address($data['to_email'], $data['to_name']);
        $this->subject = $data['subject'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getInboxId(): int
    {
        return $this->inboxId;
    }

    public function getFrom(): Address
    {
        return $this->from;
    }

    public function getTo(): Address
    {
        return $this->to;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }
}
