<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Réinitialisation de mot de passe - GEICG</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background-color: #F8FAFC; margin: 0; padding: 20px; color: #1E293B;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: 0 auto; background-color: #FFFFFF; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #E2E8F0;">
    <!-- En-tête -->
    <tr>
      <td style="background-color: #1E3A5F; padding: 32px 24px; text-align: center;">
        <h1 style="color: #FFFFFF; font-size: 22px; font-weight: 800; margin: 0; letter-spacing: 0.5px;">GEICG - OLIVE SERVICE</h1>
        <p style="color: #94A3B8; font-size: 13px; margin: 6px 0 0 0;">Demande de Réinitialisation de Mot de Passe</p>
      </td>
    </tr>

    <!-- Corps de l'email -->
    <tr>
      <td style="padding: 32px 28px;">
        <h2 style="color: #0F172A; font-size: 18px; font-weight: 700; margin-top: 0;">Bonjour <?= htmlspecialchars($userNom ?? 'Cher utilisateur') ?>,</h2>
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Nous avons reçu une demande de réinitialisation du mot de passe associé à votre compte utilisateur sur **GEICG Olive Service**.
        </p>

        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Pour définir un nouveau mot de passe sécurisé, veuillez cliquer sur le bouton ci-dessous :
        </p>

        <!-- Bouton d'action -->
        <div style="text-align: center; margin: 32px 0;">
          <a href="<?= htmlspecialchars($resetLink ?? '#') ?>" target="_blank" style="background-color: #0284C7; color: #FFFFFF; font-weight: 700; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-size: 14px; display: inline-block; box-shadow: 0 2px 6px rgba(2,132,199,0.25);">
            Réinitialiser mon mot de passe
          </a>
        </div>

        <div style="background-color: #FEF2F2; border-left: 4px solid #EF4444; padding: 16px; border-radius: 6px; margin: 24px 0; font-size: 13px; color: #991B1B;">
          <strong>⏰ Durée de validité :</strong> Ce lien de réinitialisation est à usage unique et expirera automatiquement dans <strong><?= htmlspecialchars($expirationTime ?? '30 minutes') ?></strong>.
        </div>

        <p style="font-size: 13px; color: #64748B; line-height: 1.5;">
          Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet e-mail en toute sécurité. Votre mot de passe actuel restera inchangé.
        </p>
      </td>
    </tr>

    <!-- Pied de page -->
    <tr>
      <td style="background-color: #F8FAFC; padding: 20px; text-align: center; border-top: 1px solid #E2E8F0; font-size: 12px; color: #94A3B8;">
        Cet e-mail a été envoyé automatiquement par le système GEICG.<br>
        &copy; <?= date('Y') ?> GEICG - Grande École &bull; Tous droits réservés.
      </td>
    </tr>
  </table>
</body>
</html>
