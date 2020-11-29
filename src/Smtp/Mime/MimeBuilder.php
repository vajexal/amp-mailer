<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Mime;

use Vajexal\AmpMailer\Attachment;
use Vajexal\AmpMailer\Exception\MailException;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Encoder\Body\BodyEncoder;
use Vajexal\AmpMailer\Smtp\Mime\Node\BinaryNode;
use Vajexal\AmpMailer\Smtp\Mime\Node\MultipartNode;
use Vajexal\AmpMailer\Smtp\Mime\Node\Node;
use Vajexal\AmpMailer\Smtp\Mime\Node\TextNode;

class MimeBuilder
{
    private BodyEncoder $encoder;

    public function __construct(BodyEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    public function build(Mail $mail): Node
    {
        if (!$mail->getText() && !$mail->getHtml() && !$mail->getAttachments()) {
            throw MailException::emptyMail();
        }

        if (!$mail->getText() && !$mail->getHtml()) {
            return $this->buildAttachmentsBody($mail);
        }

        $node = $this->buildTextBody($mail);

        $inlineAttachments = $this->getInlineAttachments($mail);
        $attachments       = \array_filter($mail->getAttachments(), fn ($attachment) => !\in_array($attachment, $inlineAttachments, true));

        if ($inlineAttachments) {
            $inlineNodes = \array_map(fn ($attachment) => $this->createBinaryNode($attachment), $inlineAttachments);
            $node        = new MultipartNode('related', [$node, ...$inlineNodes]);
        }

        if ($attachments) {
            $attachmentsNodes = \array_map(fn ($attachment) => $this->createBinaryNode($attachment), $attachments);
            $node             = new MultipartNode('mixed', [$node, ...$attachmentsNodes]);
        }

        return $node;
    }

    private function buildAttachmentsBody(Mail $mail): Node
    {
        return new MultipartNode(
            'related',
            \array_map(fn ($attachment) => $this->createBinaryNode($attachment), $mail->getAttachments())
        );
    }

    private function buildTextBody(Mail $mail): Node
    {
        if ($mail->getText() && $mail->getHtml()) {
            return new MultipartNode('alternative', [
                $this->createTextNode($mail->getText(), 'plain'),
                $this->createTextNode($mail->getHtml(), 'html'),
            ]);
        }

        if ($mail->getText()) {
            return $this->createTextNode($mail->getText(), 'plain');
        }

        if ($mail->getHtml()) {
            return $this->createTextNode($mail->getHtml(), 'html');
        }

        throw MailException::emptyMail();
    }

    private function getInlineAttachments(Mail $mail): array
    {
        if (!$mail->getHtml()) {
            return [];
        }

        \preg_match_all('/<img[^>]*src\s*=\s*[\'"]?cid:([^\'"\s]+)/i', $mail->getHtml(), $matches);

        return \array_filter($mail->getAttachments(), fn ($attachment) => \in_array($attachment->getId(), $matches[1], true));
    }

    private function createBinaryNode(Attachment $attachment): BinaryNode
    {
        return new BinaryNode(
            $this->encoder->encode($attachment->getContent()),
            $attachment->getMime(),
            $attachment->getFilename(),
            $this->encoder->getName(),
            $attachment->getId(),
            $attachment->getDisposition()
        );
    }

    private function createTextNode(string $content, string $subtype)
    {
        return new TextNode($this->encoder->encode($content), \sprintf('text/%s', $subtype), 'utf-8', $this->encoder->getName());
    }
}
