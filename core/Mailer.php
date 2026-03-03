<?php
namespace Caracal\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    protected PHPMailer $mailer;

    public function __construct()
    {
        $config = Application::getInstance()->config();

        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host       = $config->get('mail.host');
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $config->get('mail.user');
        $this->mailer->Password   = $config->get('mail.pass');
        $this->mailer->SMTPSecure = $config->get('mail.encryption', 'tls');
        $this->mailer->Port       = $config->get('mail.port', 587);

        $this->mailer->setFrom(
            $config->get('mail.from_address'),
            $config->get('mail.from_name')
        );

        $this->mailer->CharSet = 'UTF-8';
    }

    public function send(string $to, string $subject, string $body, bool $isHtml = true): bool
    {
        try {
            $this->mailer->clearAllRecipients();
            $this->mailer->addAddress($to);
            $this->mailer->isHTML($isHtml);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;

            $this->mailer->send();
            return true;

        } catch (Exception $e) {
            (new Logger('mailer'))->error("Mail send failed: " . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject
            ]);
            return false;
        }
    }
}