<?php

namespace DomacinskiBurek\System\Mail\Interfaces;

interface MailFactory
{
    public function setMailSender(string $sender, string $sender_name);
    public function setMailRecipient(string $recipient, string $recipient_name, string $type = '');
    public function setMailSubject(string $subject);
    public function setMailBody(string $body, ?array $params = null, bool $is_html = true, bool $is_file = true);
    public function setAttachment(array $attachment);
}