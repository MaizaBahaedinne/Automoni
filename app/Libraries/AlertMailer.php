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

        $this->mailer->setFrom(
            !empty($config->fromEmail) ? $config->fromEmail : 'noreply@persomy.com',
            !empty($config->fromName)  ? $config->fromName  : 'Persomy'
        );
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }

    public function sendInterviewNotification(object $candidate, object $interview, string $jobTitle, string $recruiterName): bool
    {
        try {
            $typeLabel = $interview->type === 'remote' ? 'Visioconférence' : 'Présentiel';
            $date      = date('d', strtotime($interview->scheduled_at)) . ' ' . lang('App.months.' . date('n', strtotime($interview->scheduled_at))) . ' ' . date('Y', strtotime($interview->scheduled_at)) . ' ' . lang('App.at_time') . ' ' . date('H:i', strtotime($interview->scheduled_at));
            $duration  = (int) $interview->duration_min;
            $location  = !empty($interview->location) ? esc($interview->location) : '—';
            $notes     = !empty($interview->notes)    ? '<p style="margin:8px 0 0;font-size:13px;color:#374151;"><strong>Notes :</strong> ' . esc($interview->notes) . '</p>' : '';
            $name      = esc($candidate->first_name);
            $jobEsc    = esc($jobTitle);
            $recruiter = esc($recruiterName);
            $locLabel  = $interview->type === 'remote' ? 'Lien de connexion' : 'Lieu';

            $this->mailer->clearAddresses();
            $this->mailer->addAddress($candidate->email, $candidate->first_name . ' ' . $candidate->last_name);
            $this->mailer->Subject = "Entretien planifié — {$jobTitle}";
            $this->mailer->Body    = <<<HTML
<!DOCTYPE html>
<html>
<body style="font-family:Arial,sans-serif;line-height:1.6;color:#1f2937;background:#f9fafb;margin:0;padding:0;">
  <div style="max-width:560px;margin:32px auto;background:#fff;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;">
    <div style="background:linear-gradient(135deg,#6366f1,#4f46e5);padding:28px 32px;">
      <h1 style="margin:0;color:#fff;font-size:20px;font-weight:700;">📅 Entretien planifié</h1>
      <p style="margin:6px 0 0;color:#c7d2fe;font-size:14px;">{$jobEsc}</p>
    </div>
    <div style="padding:28px 32px;">
      <p style="margin:0 0 16px;">Bonjour <strong>{$name}</strong>,</p>
      <p style="margin:0 0 20px;">
        Votre candidature pour le poste de <strong>{$jobEsc}</strong> a été présélectionnée.
        <strong>{$recruiter}</strong> vous invite à un entretien.
      </p>
      <div style="background:#f0f4ff;border:1px solid #c7d2fe;border-radius:8px;padding:18px 20px;margin-bottom:20px;">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
          <tr><td style="padding:4px 0;color:#6b7280;width:40%;">Type</td><td style="padding:4px 0;font-weight:600;">{$typeLabel}</td></tr>
          <tr><td style="padding:4px 0;color:#6b7280;">Date &amp; heure</td><td style="padding:4px 0;font-weight:600;">{$date}</td></tr>
          <tr><td style="padding:4px 0;color:#6b7280;">Durée</td><td style="padding:4px 0;font-weight:600;">{$duration} minutes</td></tr>
          <tr><td style="padding:4px 0;color:#6b7280;">{$locLabel}</td><td style="padding:4px 0;font-weight:600;">{$location}</td></tr>
        </table>
        {$notes}
      </div>
      <p style="font-size:13px;color:#6b7280;margin:0;">
        Si vous avez des questions, n'hésitez pas à nous contacter.
      </p>
    </div>
    <div style="background:#f9fafb;padding:16px 32px;border-top:1px solid #e5e7eb;text-align:center;">
      <p style="font-size:12px;color:#9ca3af;margin:0;">Persomy — Plateforme de recrutement</p>
    </div>
  </div>
</body>
</html>
HTML;
            $this->mailer->AltBody = "Entretien planifié pour {$jobTitle} — {$typeLabel} le {$date} ({$duration} min). Lieu/Lien : {$location}";
            return $this->mailer->send();
        } catch (Exception $e) {
            log_message('error', 'AlertMailer::sendInterviewNotification — ' . $e->getMessage());
            return false;
        }
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
