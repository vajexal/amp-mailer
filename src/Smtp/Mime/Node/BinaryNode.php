<?php

namespace Vajexal\AmpMailer\Smtp\Mime\Node;

use Vajexal\AmpMailer\Smtp\SmtpDriver;

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
        return sprintf('Content-Type: %s; name=%s', $this->contentType, $this->filename) . SmtpDriver::LB .
               sprintf('Content-Transfer-Encoding: %s', $this->encoding) . SmtpDriver::LB .
               sprintf('Content-ID: %s', $this->id) . SmtpDriver::LB .
               sprintf('Content-Disposition: %s; filename=%s', $this->disposition, $this->filename) . SmtpDriver::LB .
               SmtpDriver::LB .
               $this->content . SmtpDriver::LB;
    }
}
