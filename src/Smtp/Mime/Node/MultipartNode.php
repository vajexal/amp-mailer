<?php

namespace Vajexal\AmpMailer\Smtp\Mime\Node;

use Vajexal\AmpMailer\Smtp\SmtpDriver;

class MultipartNode implements Node
{
    private string $subtype;
    /** @var Node[] */
    private array  $nodes;
    private string $boundary;

    public function __construct(string $subtype, array $nodes)
    {
        $this->subtype  = $subtype;
        $this->nodes    = $nodes;
        // underscores to distinct boundary from base64 encoded body
        $this->boundary = \sprintf('_%s_', \bin2hex(\random_bytes((SmtpDriver::MAX_BOUNDARY_LENGTH - 2) / 2)));
    }

    public function getBody(): string
    {
        $body = \sprintf('Content-Type: multipart/%s;', $this->subtype) . SmtpDriver::LB;
        $body .= \sprintf(' boundary="%s"', $this->boundary) . SmtpDriver::LB;

        foreach ($this->nodes as $node) {
            $body .= SmtpDriver::LB . \sprintf('--%s', $this->boundary) . SmtpDriver::LB;
            $body .= $node->getBody();
        }

        $body .= \sprintf('--%s--', $this->boundary) . SmtpDriver::LB;

        return $body;
    }
}
