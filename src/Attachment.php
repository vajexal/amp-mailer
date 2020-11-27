<?php

namespace Vajexal\AmpMailer;

use Amp\File;
use Amp\Promise;
use Vajexal\AmpMailer\Exception\AttachmentException;
use Vajexal\AmpMimeType\MimeTypeGuesser;
use function Amp\call;
use function Vajexal\AmpMimeType\mimeTypeGuesser;

class Attachment
{
    const DISPOSITION_ATTACHMENT = 'attachment';
    const DISPOSITION_INLINE     = 'inline';

    const DISPOSITIONS = [
        self::DISPOSITION_ATTACHMENT,
        self::DISPOSITION_INLINE,
    ];

    private string $content;
    private string $id;
    private string $filename;
    private string $mime;
    private string $disposition = self::DISPOSITION_ATTACHMENT;

    private function __construct()
    {
    }

    public static function fromPath(string $path, string $mimeType = null): Promise
    {
        return call(function () use ($path, $mimeType) {
            if (!$mimeType) {
                /** @var MimeTypeGuesser $mimeTypeGuesser */
                $mimeTypeGuesser = yield mimeTypeGuesser();

                $mimeType = yield $mimeTypeGuesser->guess($path);
            }

            $content = yield File\get($path);

            return self::fromContent($content, \basename($path), $mimeType);
        });
    }

    public static function fromContent(string $content, string $filename, string $mimeType): self
    {
        $attachment = new self;

        $attachment->content  = $content;
        $attachment->id       = \uniqid(\mt_rand(), true);
        $attachment->filename = $filename;
        $attachment->mime     = $mimeType;

        return $attachment;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getMime(): string
    {
        return $this->mime;
    }

    public function getDisposition(): string
    {
        return $this->disposition;
    }

    public function setDisposition(string $disposition): self
    {
        if (!\in_array($disposition, self::DISPOSITIONS, true)) {
            throw AttachmentException::invalidDisposition($disposition);
        }

        $this->disposition = $disposition;

        return $this;
    }
}
