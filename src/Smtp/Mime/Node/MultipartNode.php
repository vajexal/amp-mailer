<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Mime\Node;

use const Vajexal\AmpMailer\Smtp\SMTP_LINE_BREAK;
use const Vajexal\AmpMailer\Smtp\SMTP_MAX_BOUNDARY_LENGTH;

class MultipartNode implements Node
{
    private string $subtype;
    /** @var Node[] */
    private array  $nodes;
    private string $boundary;

    public function __construct(string $subtype, array $nodes)
    {
        $this->subtype = $subtype;
        $this->nodes   = $nodes;
        // underscores to distinct boundary from base64 encoded body
        $this->boundary = \sprintf('_%s_', \bin2hex(\random_bytes((SMTP_MAX_BOUNDARY_LENGTH - 2) / 2)));
    }

    public function getBody(): string
    {
        $body = \sprintf('Content-Type: multipart/%s;', $this->subtype) . SMTP_LINE_BREAK;
        $body .= \sprintf(' boundary="%s"', $this->boundary) . SMTP_LINE_BREAK;

        foreach ($this->nodes as $node) {
            $body .= SMTP_LINE_BREAK . \sprintf('--%s', $this->boundary) . SMTP_LINE_BREAK;
            $body .= $node->getBody();
        }

        $body .= \sprintf('--%s--', $this->boundary) . SMTP_LINE_BREAK;

        return $body;
    }
}
