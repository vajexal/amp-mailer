<?php

namespace Vajexal\AmpMailer\Smtp\Mime\Node;

use Vajexal\AmpMailer\Smtp\SmtpDriver;

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
        return sprintf('Content-Type: %s; charset=%s', $this->contentType, $this->charset) . SmtpDriver::LB .
               sprintf('Content-Transfer-Encoding: %s', $this->encoding) . SmtpDriver::LB .
               SmtpDriver::LB .
               $this->content . SmtpDriver::LB;
    }
}
