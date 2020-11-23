<?php

namespace Vajexal\AmpMailer\Tests;

use Amp\File;
use Amp\PHPUnit\AsyncTestCase;
use Vajexal\AmpMailer\Smtp\Mime\Guesser\Exception\MimeTypeGuesserException;
use Vajexal\AmpMailer\Smtp\Mime\Guesser\MimeTypeGuesser;
use Vajexal\AmpMailer\Smtp\Mime\Guesser\ParallelMimeTypeGuesser;

class MimeTypeGuesserTest extends AsyncTestCase
{
    private MimeTypeGuesser $mimeTypeGuesser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mimeTypeGuesser = new ParallelMimeTypeGuesser;
    }

    public function mimeTypeGuessingProvider()
    {
        return [
            ['tests/fixtures/image.jpg', 'image/jpeg'],
            ['tests/fixtures/image.png', 'image/png'],
            ['tests/fixtures/doc.pdf', 'application/pdf'],
            ['tests/fixtures/binary', 'application/octet-stream'],
        ];
    }

    /**
     * @dataProvider mimeTypeGuessingProvider
     */
    public function testGuessing(string $path, string $expectedMimeType)
    {
        $this->assertEquals($expectedMimeType, yield $this->mimeTypeGuesser->guess($path));
    }

    public function testInvalidPath()
    {
        $this->expectException(MimeTypeGuesserException::class);
        $this->expectExceptionMessage('tests/fixtures/image.gif is not a file');

        yield $this->mimeTypeGuesser->guess('tests/fixtures/image.gif');
    }

    public function testForbiddenFile()
    {
        $this->expectException(MimeTypeGuesserException::class);
        $this->expectExceptionMessage('Could not detect mime type of tests/fixtures/forbidden.txt');

        try {
            yield File\touch('tests/fixtures/forbidden.txt');
            yield File\chmod('tests/fixtures/forbidden.txt', 0000);

            yield $this->mimeTypeGuesser->guess('tests/fixtures/forbidden.txt');
        } finally {
            yield File\unlink('tests/fixtures/forbidden.txt');
        }
    }
}
