<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail;
    public string $fromName;
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public $protocol = 'smtp';

    /**
     * The server path to Sendmail.
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     */
    public string $SMTPHost;

    /**
     * SMTP Username
     */
    public string $SMTPUser;

    /**
     * SMTP Password
     */
    public string $SMTPPass;

    /**
     * SMTP Port
     */
    public int $SMTPPort = 587;

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = 5;

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive = false;

    /**
     * SMTP Encryption.
     *
     * @var string '', 'tls' or 'ssl'. 'tls' will issue a STARTTLS command
     *             to the server. 'ssl' means implicit SSL. Connection on port
     *             465 should set this to ''.
     */
    public string $SMTPCrypto = 'tls';

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $mailType = 'html';

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = false;

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     */
    public int $priority = 3;

    /**
     * Newline character. (Use "\r\n" to comply with RFC 822)
     */
    public string $CRLF = "\r\n";

    /**
     * Newline character. (Use "\r\n" to comply with RFC 822)
     */
    public string $newline = "\r\n";

    /**
     * Enable BCC Batch Mode.
     */
    public bool $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public int $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     */
    public bool $DSN = false;

    /**
     * Pull every SMTP value from .env so you never have to
     * hard-code credentials in this file.
     */
    public function __construct()
    {
        parent::__construct();

        $this->SMTPHost   = getenv('EMAIL_HOST')       ?: 'smtp.gmail.com';
        $this->SMTPUser   = getenv('EMAIL_HOST_USER')  ?: '';
        $this->SMTPPass   = getenv('EMAIL_SMTP_PASS')  ?: '';
        $this->SMTPPort   = (int)(getenv('EMAIL_PORT') ?: 587);
        $this->SMTPCrypto = getenv('EMAIL_SMTP_CRYPTO') ?: 'tls';
        $this->fromEmail  = getenv('EMAIL_FROM_EMAIL') ?: $this->SMTPUser;
        $this->fromName   = getenv('EMAIL_FROM_NAME')  ?: 'MedEquip';
        $this->mailType   = getenv('EMAIL_MAIL_TYPE')  ?: 'html';
        $this->charset    = getenv('EMAIL_CHARSET')    ?: 'UTF-8';
    }
}