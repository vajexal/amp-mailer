<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Mime\Node;

use const Vajexal\AmpMailer\Smtp\SMTP_LINE_BREAK;

class TextNode implements Node
{
    private string $content;
    private string $contentType;
    private string $charset;
    private string $encoding;

    public function __construct(string $content, string $contentType, string $charset, string $encoding)
    {
        $this->content     = $content;
        $this->contentType = $contentType;
        $this->charset     = $charset;
        $this->encoding    = $encoding;
    }

    public function getBody(): string
    {
        return \sprintf('Content-Type: %s; charset=%s', $this->contentType, $this->charset) . SMTP_LINE_BREAK .
               \sprintf('Content-Transfer-Encoding: %s', $this->encoding) . SMTP_LINE_BREAK .
               SMTP_LINE_BREAK .
               $this->content . SMTP_LINE_BREAK;
    }
}
