<?php

namespace Vajexal\AmpMailer;

class Mail
{
    private ?Address $from = null;
    /** @var Address[] */
    private array $replyTo = [];
    /** @var Address[] */
    private array $to = [];
    /** @var Address[] */
    private array $cc = [];
    /** @var Address[] */
    private array   $bcc     = [];
    private ?string $subject = null;
    private ?string $text    = null;
    private ?string $html    = null;
    /** @var Attachment[] */
    private array $attachments = [];

    public function getFrom(): ?Address
    {
        return $this->from;
    }

    public function from(string $email, string $name = null): self
    {
        $this->from = new Address($email, $name);

        return $this;
    }

    public function getReplyTo(): array
    {
        return $this->replyTo;
    }

    public function replyTo(string $email, string $name = null): self
    {
        $this->replyTo[] = new Address($email, $name);

        return $this;
    }

    public function getTo(): array
    {
        return $this->to;
    }

    public function to(string $email, string $name = null): self
    {
        $this->to[] = new Address($email, $name);

        return $this;
    }

    public function setTo(array $addresses): self
    {
        $this->to = $addresses;

        return $this;
    }

    public function getCc(): array
    {
        return $this->cc;
    }

    public function cc(string $email, string $name = null): self
    {
        $this->cc[] = new Address($email, $name);

        return $this;
    }

    public function getBcc(): array
    {
        return $this->bcc;
    }

    public function bcc(string $email, string $name = null): self
    {
        $this->bcc[] = new Address($email, $name);

        return $this;
    }

    public function setBcc(array $addresses): self
    {
        $this->bcc = $addresses;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function text(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function html(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function attach(Attachment $attachment): self
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    public function inline(Attachment $attachment): self
    {
        $attachment->setDisposition(Attachment::DISPOSITION_INLINE);

        $this->attachments[] = $attachment;

        return $this;
    }

    public function embed(Attachment $attachment): string
    {
        $this->inline($attachment);

        return 'cid:' . $attachment->getId();
    }
}
