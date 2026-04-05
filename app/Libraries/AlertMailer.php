<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AlertMailer
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $config = config('Email');
        $this->mailer = new PHPMailer(true);

        // Use SMTP if configured, otherwise use PHP mail()
        if (!empty($config->SMTPHost)) {
            $this->mailer->isSMTP();
            $this->mailer->Host       = $config->SMTPHost;
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = $config->SMTPUser;
            $this->mailer->Password   = $config->SMTPPass;
            $this->mailer->SMTPSecure = $config->SMTPCrypto === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = (int) $config->SMTPPort;
        }

        $this->mailer->setFrom($config->fromEmail ?? 'noreply@persomy.local', $config->fromName ?? 'Persomy');
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }

    public function sendJobAlert(object $user, object $job, object $alert): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($user->email, $user->first_name . ' ' . $user->last_name);
            $this->mailer->Subject = 'New job matching your alert: ' . $job->title;
            $this->mailer->Body    = $this->buildAlertHtml($user, $job);
            $this->mailer->AltBody = "New job: {$job->title} at {$job->company_name} — " . site_url('jobs/' . $job->slug);
            return $this->mailer->send();
        } catch (Exception $e) {
            log_message('error', 'AlertMailer: ' . $e->getMessage());
            return false;
        }
    }

    private function buildAlertHtml(object $user, object $job): string
    {
        $jobUrl  = site_url('jobs/' . $job->slug);
        $name    = esc($user->first_name);
        $title   = esc($job->title);
        $company = esc($job->company_name ?? '');
        $loc     = esc($job->location ?? 'Remote');
        $ctype   = esc($job->contract_type);

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <body style="font-family:Arial,sans-serif;line-height:1.6;color:#333">
          <h2 style="color:#2563eb">Hi {$name},</h2>
          <p>A new job matching your alert has just been posted:</p>
          <div style="border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin:16px 0">
            <h3 style="margin:0 0 8px">{$title}</h3>
            <p style="margin:0;color:#6b7280">{$company} &bull; {$loc} &bull; {$ctype}</p>
          </div>
          <p><a href="{$jobUrl}" style="background:#2563eb;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none">View Job</a></p>
          <p style="font-size:12px;color:#9ca3af">You're receiving this because you set up a job alert on Persomy. 
             <a href="{$_SERVER['HTTP_HOST']}/alerts">Manage alerts</a></p>
        </body>
        </html>
        HTML;
    }
}
