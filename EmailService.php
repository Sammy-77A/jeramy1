<?php
// includes/EmailService.php
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $smtpHost = 'mail.jeramy1.top';
    private $smtpPort = 465;
    private $smtpUsername = 'support@jeramy1.top';
    private $smtpPassword = 'T2FZ7)U52bkq;e';
    private $fromEmail = 'support@jeramy1.top';
    private $fromName = 'Jera Moyie SHG';

    public function sendPasswordResetEmail($toEmail, $toName, $resetToken, $userId)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->smtpPort;

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);

            // --- LIVE URL ONLY ---
            $resetLink = "https://jeramy1.top/reset-password.php?token=" . urlencode($resetToken) . "&user=" . $userId;

            $mail->isHTML(true);
            $mail->Subject = "Reset Your Password - Jera Moyie";
            $mail->Body = $this->getTemplate($toName, $resetLink);
            $mail->AltBody = "Hello $toName, reset your password here: $resetLink";

            $mail->send();
            return ['success' => true];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $mail->ErrorInfo];
        }
    }

    public function sendCommunityPasswordResetEmail($toEmail, $toName, $resetToken, $userId)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->smtpPort;

            $mail->setFrom($this->fromEmail, 'Jera Moyie Community');
            $mail->addAddress($toEmail, $toName);

            $resetLink = "https://jeramy1.top/community/reset-password.php?token=" . urlencode($resetToken) . "&user=" . $userId;

            $mail->isHTML(true);
            $mail->Subject = "Reset Your Password - Jera Moyie Community";
            $mail->Body = $this->getTemplate($toName, $resetLink);
            $mail->AltBody = "Hello $toName, reset your community password here: $resetLink";

            $mail->send();
            return ['success' => true];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $mail->ErrorInfo];
        }
    }

    /**
     * Sends a security alert after a successful password change
     */
    public function sendPasswordChangedAlert($toEmail, $toName)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->smtpPort;

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = "Security Alert: Password Changed - Jera Moyie SHG";

            $mail->Body = "
            <div style='font-family: sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 10px; overflow: hidden;'>
                <div style='background: #f59e0b; color: white; padding: 25px; text-align: center;'>
                    <h2 style='margin:0;'>Security Notification</h2>
                </div>
                <div style='padding: 30px; line-height: 1.6; color: #333;'>
                    <p>Hello <strong>$toName</strong>,</p>
                    <p>This is a confirmation that the password for your <strong>Jera Moyie SHG</strong> account was recently changed.</p>
                    <div style='background: #fff8e1; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0;'>
                        <strong>If you did not make this change</strong>, please contact our support team immediately at <a href='tel:+254726773296'>+254 726 773 296</a> to secure your account.
                    </div>
                    <p style='font-size: 12px; color: #888;'>This is an automated security notification. You do not need to reply.</p>
                </div>
            </div>";

            $mail->send();
            return ['success' => true];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $mail->ErrorInfo];
        }
    }

    /**
     * Sends a professional weekly meeting reminder
     */
    public function sendMeetingReminder($toEmail, $toName)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->smtpPort;

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = "Weekly Group Meeting Reminder - Jera Moyie SHG";

            $mail->Body = "
            <div style='font-family: sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 10px; overflow: hidden;'>
                <div style='background: #2c7a4b; color: white; padding: 25px; text-align: center;'>
                    <h1 style='margin:0; font-family: serif;'>Jera Moyie SHG</h1>
                    <p style='margin:5px 0 0; opacity: 0.9;'>Empowering Communities Since 2005</p>
                </div>
                <div style='padding: 30px; line-height: 1.6; color: #333;'>
                    <p>Hello <strong>$toName</strong>,</p>
                    <p>This is a friendly reminder for our <strong>Weekly Group Meeting</strong> held at Kibuye Market, Gate 3, between 4:00 PM and 6:00 PM.</p>
                    
                    <div style='background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2c7a4b;'>
                        <h4 style='color: #2c7a4b; margin-top: 0;'>Building Our Future Together</h4>
                        <p style='margin-bottom: 10px;'>Your active participation is vital as we continue to grow through our core pillars:</p>
                        <ul style='padding-left: 20px; margin: 0;'>
                            <li><strong>Smart Savings:</strong> Secure plans with no hidden fees and annual dividends.</li>
                            <li><strong>Table Banking:</strong> Access to instant credit up to 3x your savings.</li>
                            <li><strong>Social Welfare:</strong> Mutual support during hospitalization and emergencies.</li>
                        </ul>
                    </div>

                    <p>We value your consistency in making Jera Moyie Kisumu's most trusted self-help group. See you there!</p>
                    
                    <div style='text-align: center; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;'>
                        <p style='font-size: 12px; color: #888;'>Questions? Call us at +254 726 773 296 or join our WhatsApp Group for updates.</p>
                        <p style='font-size: 11px; color: #aaa;'>&copy; " . date('Y') . " Jera Moyie Self-Help Group. All rights reserved.</p>
                    </div>
                </div>
            </div>";

            $mail->send();
            return ['success' => true];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $mail->ErrorInfo];
        }
    }

    private function getTemplate($name, $link)
    {
        return "
        <div style='font-family: sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 10px; overflow: hidden;'>
            <div style='background: #2c7a4b; color: white; padding: 25px; text-align: center;'>
                <h2 style='margin:0;'>Jera Moyie SHG</h2>
            </div>
            <div style='padding: 30px; line-height: 1.6; color: #333;'>
                <p>Hello <strong>$name</strong>,</p>
                <p>We received a request to reset your password. This link is valid for <strong>30 minutes</strong> and can only be used <strong>once</strong>.</p>
                <div style='text-align: center; margin: 30px;'>
                    <a href='$link' style='background: #2c7a4b; color: white; padding: 14px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset My Password</a>
                </div>
                <p style='font-size: 12px; color: #888; text-align: center;'>If you did not request this, please ignore this email.</p>
            </div>
        </div>";
    }

    /**
     * Sends a generic system notification
     */
    public function sendNotificationEmail($toEmail, $toName, $subject, $htmlBody)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->smtpPort;

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = $subject;

            $mail->Body = "
            <div style='font-family: sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 10px; overflow: hidden;'>
                <div style='background: #2c7a4b; color: white; padding: 25px; text-align: center;'>
                    <h2 style='margin:0; font-family: serif;'>Jera Moyie SHG</h2>
                    <p style='margin:5px 0 0; opacity: 0.9;'>System Notification</p>
                </div>
                <div style='padding: 30px; line-height: 1.6; color: #333;'>
                    <p>Hello <strong>$toName</strong>,</p>
                    $htmlBody
                    <div style='text-align: center; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;'>
                        <p style='font-size: 12px; color: #888;'>This is an automated system notification.</p>
                        <p style='font-size: 11px; color: #aaa;'>&copy; " . date('Y') . " Jera Moyie Self-Help Group. All rights reserved.</p>
                    </div>
                </div>
            </div>";

            $mail->send();
            return ['success' => true];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $mail->ErrorInfo];
        }
    }
}
