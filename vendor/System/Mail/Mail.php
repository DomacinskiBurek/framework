<?php

namespace DomacinskiBurek\System\Mail;

use DomacinskiBurek\System\Config;
use DomacinskiBurek\System\Mail\Interfaces\MailFactory;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

abstract class Mail implements MailFactory
{
    private string $config;
    private PHPMailer $service;

    /**
     * @throws Exception
     */
    public function __construct ()
    {
        $this->service = new PHPMailer();
        $this->connectService($this->service);
    }

    public function getService (): PHPMailer
    {
        return $this->service;
    }

    public function setConfig (string $configName) : void
    {
        $this->config = $configName;
    }

    /**
     * @throws Exception
     */
    private function connectService (PHPMailer $service) : void
    {
        $config = new Config();
        $config->load($this->config, "yaml");
        $settings = $config->get($this->config);

        $service->SMTPDebug = $settings["SMTP_DEBUG"];
        $service->isSMTP();
        $service->Host       = $settings["SMTP_SERVER"];
        $service->SMTPAuth   = (bool) $settings["SMTP_AUTH"];
        $service->Username   = $settings["SMTP_USER"];
        $service->Password   = $settings["SMTP_PASSWORD"];
        $service->SMTPSecure = ($settings["SMTP_SECURE"] == 'tls') ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $service->Port       = $settings["SMTP_PORT"];
        $service->setFrom($settings["SMTP_SENDER"], $settings["SMTP_SENDER_NAME"]);
    }

    abstract public function send();
    abstract protected function fillOut(string &$body, array $params);
}