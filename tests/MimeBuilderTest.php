<?php

namespace Vajexal\AmpMailer\Tests;

use Amp\File;
use Vajexal\AmpMailer\Attachment;
use Vajexal\AmpMailer\DiLocator;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Mime\MimeBuilder;

class MimeBuilderTest extends TestCase
{
    private MimeBuilder $mimeBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mimeBuilder = DiLocator::mimeBuilder();
    }

    public function testBuildComplexBody()
    {
        $mail = (new Mail)
            ->from('foo@example.com')
            ->to('bar@example.com')
            ->text('Test')
            ->attach(yield Attachment::fromPath('tests/fixtures/doc.pdf'));

        $image = yield Attachment::fromPath('tests/fixtures/image.jpg');
        // to generate predictable html body
        setPrivateProperty($image, 'id', '9439428065fb3a37788e3a7.47570044');

        $mail->html(sprintf('Test <img src="%s" alt="Image example">', $mail->embed($image)));

        $body = $this->mimeBuilder->build($mail)->getBody();

        $pattern = yield File\get('tests/dumps/complex.txt');

        $this->assertOutputMatchesPattern($pattern, $body);
    }

    public function testBuildAttachment()
    {
        $mail = (new Mail)
            ->from('foo@example.com')
            ->to('bar@example.com')
            ->attach(yield Attachment::fromPath('tests/fixtures/doc.pdf'));

        $body = $this->mimeBuilder->build($mail)->getBody();

        $pattern = yield File\get('tests/dumps/attachment.txt');

        $this->assertOutputMatchesPattern($pattern, $body);
    }
}
