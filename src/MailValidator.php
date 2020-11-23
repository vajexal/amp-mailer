<?php

namespace Vajexal\AmpMailer;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\EmailValidation;
use Vajexal\AmpMailer\Exception\EmailException;
use Vajexal\AmpMailer\Exception\MailException;

class MailValidator
{
    private EmailValidator  $validator;
    private EmailValidation $rule;

    public function __construct(EmailValidator $validator, EmailValidation $rule)
    {
        $this->validator = $validator;
        $this->rule      = $rule;
    }

    public function validate(Mail $mail): void
    {
        if (!$mail->getFrom()) {
            throw MailException::emptyFrom();
        }

        if (!$mail->getTo() && !$mail->getBcc()) {
            throw MailException::emptyRecipients();
        }

        if (!$mail->getText() && !$mail->getHtml() && !$mail->getAttachments()) {
            throw MailException::emptyMail();
        }

        $this->validateEmail($mail->getFrom()->getEmail());

        foreach ($mail->getReplyTo() as $address) {
            $this->validateEmail($address->getEmail());
        }

        foreach ($mail->getTo() as $address) {
            $this->validateEmail($address->getEmail());
        }

        foreach ($mail->getCc() as $address) {
            $this->validateEmail($address->getEmail());
        }

        foreach ($mail->getBcc() as $address) {
            $this->validateEmail($address->getEmail());
        }
    }

    private function validateEmail(string $email): void
    {
        if (!$this->validator->isValid($email, $this->rule)) {
            throw EmailException::invalidEmail($email);
        }
    }
}
