<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Vajexal\AmpMailer\Smtp\AddressFormatter;
use Vajexal\AmpMailer\Smtp\Command\Auth\CramMd5AuthStrategy;
use Vajexal\AmpMailer\Smtp\Command\Auth\LoginAuthStrategy;
use Vajexal\AmpMailer\Smtp\Command\Auth\PlainAuthStrategy;
use Vajexal\AmpMailer\Smtp\Command\AuthCommand;
use Vajexal\AmpMailer\Smtp\Command\DataCommand;
use Vajexal\AmpMailer\Smtp\Command\EhloCommand;
use Vajexal\AmpMailer\Smtp\Command\Extension\AuthExtension;
use Vajexal\AmpMailer\Smtp\Command\Extension\SizeExtension;
use Vajexal\AmpMailer\Smtp\Command\Extension\TlsExtension;
use Vajexal\AmpMailer\Smtp\Command\Header\BccHeader;
use Vajexal\AmpMailer\Smtp\Command\Header\CcHeader;
use Vajexal\AmpMailer\Smtp\Command\Header\DateHeader;
use Vajexal\AmpMailer\Smtp\Command\Header\FromHeader;
use Vajexal\AmpMailer\Smtp\Command\Header\MessageIdHeader;
use Vajexal\AmpMailer\Smtp\Command\Header\MimeVersionHeader;
use Vajexal\AmpMailer\Smtp\Command\Header\ReplyToHeader;
use Vajexal\AmpMailer\Smtp\Command\Header\SubjectHeader;
use Vajexal\AmpMailer\Smtp\Command\Header\ToHeader;
use Vajexal\AmpMailer\Smtp\Command\HeloCommand;
use Vajexal\AmpMailer\Smtp\Command\MailCommand;
use Vajexal\AmpMailer\Smtp\Command\QuitCommand;
use Vajexal\AmpMailer\Smtp\Command\RecipientCommand;
use Vajexal\AmpMailer\Smtp\Command\StartTlsCommand;
use Vajexal\AmpMailer\Smtp\Encoder\Body\Base64Encoder;
use Vajexal\AmpMailer\Smtp\Encoder\Body\BodyEncoder;
use Vajexal\AmpMailer\Smtp\Encoder\Email\EmailEncoder;
use Vajexal\AmpMailer\Smtp\Encoder\Email\PunycodeEmailEncoder;
use Vajexal\AmpMailer\Smtp\Encoder\Header\HeaderEncoder;
use Vajexal\AmpMailer\Smtp\Encoder\Header\QpMimeEncoder;
use Vajexal\AmpMailer\Smtp\Mime\MimeBuilder;

class DiLocator
{
    public static function ehloCommand(): EhloCommand
    {
        return new EhloCommand(self::hostDetector());
    }

    public static function heloCommand(): HeloCommand
    {
        return new HeloCommand(self::hostDetector());
    }

    public static function startTlsCommand(): StartTlsCommand
    {
        return new StartTlsCommand;
    }

    public static function authCommand(): AuthCommand
    {
        return new AuthCommand;
    }

    public static function mailCommand(): MailCommand
    {
        return new MailCommand(self::emailEncoder());
    }

    public static function recipientCommand(): RecipientCommand
    {
        return new RecipientCommand(self::emailEncoder());
    }

    public static function dataCommand(): DataCommand
    {
        return new DataCommand(self::emailEncoder(), self::headerEncoder(), self::mimeBuilder());
    }

    public static function quitCommand(): QuitCommand
    {
        return new QuitCommand();
    }

    public static function emailEncoder(): EmailEncoder
    {
        return new PunycodeEmailEncoder;
    }

    public static function headerEncoder(): HeaderEncoder
    {
        return new QpMimeEncoder;
    }

    public static function bodyEncoder(): BodyEncoder
    {
        return new Base64Encoder;
    }

    public static function mimeBuilder(): MimeBuilder
    {
        return new MimeBuilder(self::bodyEncoder());
    }

    public static function hostDetector(): HostDetector
    {
        return new HostDetector;
    }

    public static function addressFormatter(): AddressFormatter
    {
        return new AddressFormatter(self::emailEncoder(), self::headerEncoder());
    }

    public static function dateHeader(): DateHeader
    {
        return new DateHeader;
    }

    public static function fromHeader(): FromHeader
    {
        return new FromHeader(self::addressFormatter());
    }

    public static function replyToHeader(): ReplyToHeader
    {
        return new ReplyToHeader(self::addressFormatter());
    }

    public static function toHeader(): ToHeader
    {
        return new ToHeader(self::addressFormatter());
    }

    public static function ccHeader(): CcHeader
    {
        return new CcHeader(self::addressFormatter());
    }

    public static function bccHeader(): BccHeader
    {
        return new BccHeader(self::addressFormatter());
    }

    public static function messageIdHeader(): MessageIdHeader
    {
        return new MessageIdHeader(self::hostDetector());
    }

    public static function subjectHeader(): SubjectHeader
    {
        return new SubjectHeader(self::headerEncoder());
    }

    public static function mimeVersionHeader(): MimeVersionHeader
    {
        return new MimeVersionHeader;
    }

    public static function authExtension(): AuthExtension
    {
        return new AuthExtension;
    }

    public static function tlsExtension(): TlsExtension
    {
        return new TlsExtension;
    }

    public static function sizeExtension(): SizeExtension
    {
        return new SizeExtension;
    }

    public static function cramMd5AuthStrategy(): CramMd5AuthStrategy
    {
        return new CramMd5AuthStrategy;
    }

    public static function loginAuthStrategy(): LoginAuthStrategy
    {
        return new LoginAuthStrategy;
    }

    public static function plainAuthStrategy(): PlainAuthStrategy
    {
        return new PlainAuthStrategy;
    }

    public static function mailValidator(): MailValidator
    {
        return new MailValidator(new EmailValidator, new RFCValidation);
    }
}
