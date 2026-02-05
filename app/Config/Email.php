<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail = 'no-reply@assetiq.com';
    public string $fromName  = 'AssetIQ';
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * Use PHP mail() function
     */
    public string $protocol = 'mail';

    /**
     * Path to sendmail (used internally by PHP mail)
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail
     */
    public string $mailType = 'html';

    /**
     * Character set
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = false;

    /**
     * Email Priority
     */
    public int $priority = 3;

    /**
     * Newline characters
     */
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";

    /**
     * Enable BCC Batch Mode
     */
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize = 200;

    /**
     * Enable delivery status notification
     */
    public bool $DSN = false;
}
