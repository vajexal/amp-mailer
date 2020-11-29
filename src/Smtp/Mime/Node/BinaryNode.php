<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Mime\Node;

use const Vajexal\AmpMailer\Smtp\SMTP_LINE_BREAK;

class BinaryNode implements Node
{
    private string $content;
    private string $contentType;
    private string $filename;
    private string $encoding;
    private string $id;
    private string $disposition;

    public function __construct(string $content, string $contentType, string $filename, string $encoding, string $id, string $disposition)
    {
        $this->content     = $content;
        $this->contentType = $contentType;
        $this->filename    = $filename;
        $this->encoding    = $encoding;
        $this->id          = $id;
        $this->disposition = $disposition;
    }

    public function getBody(): string
    {
        return \sprintf('Content-Type: %s; name=%s', $this->contentType, $this->filename) . SMTP_LINE_BREAK .
               \sprintf('Content-Transfer-Encoding: %s', $this->encoding) . SMTP_LINE_BREAK .
               \sprintf('Content-ID: %s', $this->id) . SMTP_LINE_BREAK .
               \sprintf('Content-Disposition: %s; filename=%s', $this->disposition, $this->filename) . SMTP_LINE_BREAK .
               SMTP_LINE_BREAK .
               $this->content . SMTP_LINE_BREAK;
    }
}
