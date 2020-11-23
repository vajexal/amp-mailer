<?php

namespace Vajexal\AmpMailer\Smtp\Mime\Guesser;

use Amp\File;
use Amp\Promise;
use Vajexal\AmpMailer\Smtp\Mime\Guesser\Exception\MimeTypeGuesserException;
use function Amp\call;
use function Amp\ParallelFunctions\parallel;

class ParallelMimeTypeGuesser implements MimeTypeGuesser
{
    public function guess(string $path): Promise
    {
        return call(function () use ($path) {
            if (!(yield File\isfile($path))) {
                throw MimeTypeGuesserException::invalidPath($path);
            }

            $guesser = parallel(fn ($path) => @\mime_content_type($path));

            $mimeType = yield $guesser($path);

            if ($mimeType === false) {
                throw MimeTypeGuesserException::couldNotDetect($path);
            }

            return $mimeType;
        });
    }
}
