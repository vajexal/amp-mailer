<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp;

const SMTP_MIME_MAX_LINE_LENGTH = 76;
const SMTP_MAX_BOUNDARY_LENGTH  = 70;
const SMTP_LINE_BREAK           = "\r\n";

const QP_MIME_BASE64_SCHEME           = 'B';
const QP_MIME_QUOTED_PRINTABLE_SCHEME = 'Q';
