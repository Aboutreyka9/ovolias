<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService
{
    /**
     * Envoie un e-mail au format HTML ou Texte via PHPMailer et la configuration LWS SMTP (.env)
     * 
     * @param string $toEmail Adresse du destinataire
     * @param string $subject Sujet du message
     * @param string $htmlBody Contenu HTML du message
     * @param string $altText Texte alternatif sans HTML (optionnel)
     * @return array ['status' => bool, 'message' => string]
     */
    public static function send(string $toEmail, string $subject, string $htmlBody, string $altText = ''): array
    {
        $mail = new PHPMailer(true);

        try {
            // Configuration SMTP LWS extraite de .env
            $host       = $_ENV['MAIL_HOST'] ?? ($_SERVER['MAIL_HOST'] ?? 'mail.groupe-eicg.net');
            $port       = (int)($_ENV['MAIL_PORT'] ?? ($_SERVER['MAIL_PORT'] ?? 465));
            $username   = $_ENV['MAIL_USERNAME'] ?? ($_SERVER['MAIL_USERNAME'] ?? '');
            $password   = $_ENV['MAIL_PASSWORD'] ?? ($_SERVER['MAIL_PASSWORD'] ?? '');
            $encryption = $_ENV['MAIL_ENCRYPTION'] ?? ($_SERVER['MAIL_ENCRYPTION'] ?? 'ssl');
            $fromAddr   = $_ENV['MAIL_FROM_ADDRESS'] ?? ($_SERVER['MAIL_FROM_ADDRESS'] ?? 'noreply@groupe-eicg.net');
            $fromName   = $_ENV['MAIL_FROM_NAME'] ?? ($_SERVER['MAIL_FROM_NAME'] ?? 'GEICG - Olive Service');

            // Paramètres Serveur SMTP
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $username;
            $mail->Password   = $password;
            $mail->SMTPSecure = strtolower($encryption) === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $port;
            $mail->CharSet    = 'UTF-8';

            // Adresses
            $mail->setFrom($fromAddr, $fromName);
            $mail->addAddress($toEmail);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $altText ?: strip_tags($htmlBody);

            $mail->send();
            return ['status' => true, 'message' => 'E-mail envoyé avec succès.'];
        } catch (Exception $e) {
            return ['status' => false, 'message' => "Erreur d'envoi d'e-mail : " . $mail->ErrorInfo];
        }
    }

    /**
     * Envoie un e-mail basé sur un template HTML situé dans views/templates/emails/
     * 
     * @param string $toEmail Adresse du destinataire
     * @param string $subject Sujet du message
     * @param string $templateName Nom du template (ex: 'welcome_credentials' ou 'reset_password')
     * @param array $data Données transmises au template (ex: ['userNom' => 'Jean', 'loginUrl' => '...'])
     * @return array ['status' => bool, 'message' => string]
     */
    public static function sendTemplate(string $toEmail, string $subject, string $templateName, array $data = []): array
    {
        $templatePath = __DIR__ . '/../views/templates/emails/' . ltrim($templateName, '/\\') . '.php';

        if (!file_exists($templatePath)) {
            return ['status' => false, 'message' => "Le template d'email [$templateName] est introuvable."];
        }

        extract($data);

        ob_start();
        require $templatePath;
        $htmlBody = ob_get_clean();

        return self::send($toEmail, $subject, $htmlBody);
    }
}
