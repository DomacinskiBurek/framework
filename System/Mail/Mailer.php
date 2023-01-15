<?php

namespace DomacinskiBurek\System\Mail;

use DomacinskiBurek\System\Error\Handlers\StreamException;
use DomacinskiBurek\System\Filesystem\File;
use PHPMailer\PHPMailer\Exception;

class Mailer extends Mail
{
    public function __construct(string $configName)
    {
        $this->setConfig($configName);

        parent::__construct();
    }

    /**
     * @throws Exception
     */
    function send()
    {
        $this->getService()->send();
    }

    /**
     * @throws Exception
     */
    public function setMailSender(string $sender, string $sender_name): self
    {
        $this->getService()->setFrom($sender, $sender_name);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function setMailRecipient(string $recipient, string $recipient_name, string $type = ''): self
    {
        switch ($type) {
            case '':
                $this->getService()->addAddress($recipient, $recipient_name);
                break;
            case 'cc':
                $this->getService()->addCC($recipient, $recipient);
                break;
            case 'bcc':
                $this->getService()->addBCC($recipient, $recipient);
                break;
        }
        return $this;
    }

    public function setMailSubject(string $subject): self
    {
        $this->getService()->Subject = $subject;
        return $this;
    }

    /**
     * @throws StreamException
     */
    public function setMailBody(string $body, ?array $params = null, bool $is_html = true, bool $is_file = true): self
    {
        if ($is_file === true) $body = $this->fetchTemplate($body);
        if (is_null($params) === false) $this->fillOut($body, $params);

        $this->getService()->isHTML($is_html);
        $this->getService()->Body = $body;
        return $this;
    }

    public function setAttachment(array $attachment)
    {
        // TODO: Implement setAttachment() method.
    }

    protected function fillOut(string &$body, array $params): void
    {
        foreach ($params as $shape => $live) {
            if (str_contains($body, $shape)) $body = str_replace($shape, $live, $body);
        }
    }

    /**
     * @throws StreamException
     */
    private function fetchTemplate (string $name)
    {
        $file = __DIR__ . "/Templates/$name.html";

        if (is_file($file) && is_readable($file)) {
            $file = new File($file);

            return $file->fread($file->getSize());
        }

        throw new StreamException("could not read template file", 500);
    }
}