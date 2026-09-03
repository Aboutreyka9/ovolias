<?php
try {
    $db = (new Database())->getCon();
    $stmt = $db->query("SELECT logo_etablissement, libelle_etablissement FROM etablissements ORDER BY id_etablissement ASC LIMIT 1");
    $etabRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $loginLogo = $etabRow['logo_etablissement'] ?? '';
    $etabLibelle = $etabRow['libelle_etablissement'] ?? 'GEICG - Grande École';
} catch (Exception $e) {
    $loginLogo = '';
    $etabLibelle = 'GEICG - Grande École';
}
?>

<style>
  body {
    background-color: #f3f4f6;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
  }

  body::-webkit-scrollbar {
    display: none !important;
  }

  .login-wrapper {
    height: 100vh;
    width: 100vw;
    display: flex;
    flex-wrap: nowrap;
    overflow: hidden;
  }

  .content-left {
    background-color: #f2f6ff;
    width: 60%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .image-wrapper {
    background-image: linear-gradient(rgba(18, 38, 68, 0.45), rgba(15, 23, 42, 0.55)), url('<?= RACINE ?>public/assets/images/bg/cover.png');
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    width: 100%;
    height: 100%;
  }

  .content-right {
    width: 40%;
    background-color: #f3f4f6;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow-y: auto;
    padding: 24px;
    box-sizing: border-box;
  }

  .wrap-content {
    width: 100%;
    max-width: 440px;
  }

  .writer-login {
    background-color: #ffffff;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    padding: 36px 32px;
    border-radius: 12px;
    box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #E2E8F0;
    box-sizing: border-box;
    width: 100%;
  }

  .logo-login-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 16px;
  }

  .title-login {
    text-align: center;
    margin-bottom: 24px;
  }

  .title-login h3 {
    color: #1E3A5F;
    font-size: 24px;
    font-weight: 800;
    margin: 0;
    letter-spacing: 0.5px;
  }

  .title-login p {
    color: #64748B;
    font-weight: 500;
    font-size: 13.5px;
    margin: 6px 0 0 0;
    line-height: 1.4;
  }

  .input-wrapper {
    width: 100%;
  }

  .input-wrapper form {
    width: 100%;
    margin: 0;
    display: block;
  }

  .form-group-login {
    margin-bottom: 18px;
    width: 100%;
    box-sizing: border-box;
  }

  .form-group-login label {
    display: block;
    font-weight: 700;
    font-size: 13px;
    color: #334155;
    margin-bottom: 8px;
    text-align: left;
  }

  .input-icon-container {
    position: relative;
    width: 100%;
    box-sizing: border-box;
  }

  .input-icon-container .input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748B;
    display: flex;
    align-items: center;
    pointer-events: none;
    z-index: 2;
  }

  .input-icon-container .form-control {
    width: 100%;
    padding: 12px 14px 12px 42px;
    font-size: 14px;
    border-radius: 8px;
    border: 1px solid #CBD5E1;
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.2s, box-shadow 0.2s;
    background-color: #FFFFFF;
    color: #1E293B;
  }

  .input-icon-container .form-control:focus {
    border-color: #1E3A5F;
    box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
  }

  .btn-login-submit {
    width: 100%;
    background: #0284C7;
    border: 1px solid #0284C7;
    color: #FFFFFF;
    font-weight: 700;
    font-size: 14.5px;
    padding: 13px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-sizing: border-box;
  }

  .btn-login-submit:hover {
    background: #0369A1;
    border-color: #0369A1;
  }

  .back-to-login {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    color: #2563EB;
    font-size: 13.5px;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s;
  }

  .back-to-login:hover {
    color: #1D4ED8;
    text-decoration: underline;
  }

  @media (max-width: 768px) {
    .content-left { display: none; }
    .content-right { width: 100%; padding: 16px; }
    .writer-login { padding: 28px 20px; }
  }
</style>

<div class="login-wrapper">

  <!-- CÔTÉ GAUCHE : COVER -->
  <div class="content-left">
    <div class="image-wrapper"></div>
  </div>

  <!-- CÔTÉ DROIT : FORMULAIRE NOUVEAU MOT DE PASSE -->
  <div class="content-right">
    <div class="wrap-content">
      <div class="writer-login">

        <!-- LOGO -->
        <div class="logo-login-wrapper">
          <?php if (!empty($loginLogo)): ?>
            <?php $logoUrl = (strpos($loginLogo, 'http') === 0) ? $loginLogo : RACINE . ltrim($loginLogo, '/'); ?>
            <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo" style="max-height: 55px; max-width: 200px; object-fit: contain;">
          <?php else: ?>
            <div style="font-size: 24px; font-weight: 900; color: #1E3A5F; letter-spacing: 1px; display: flex; align-items: center; gap: 8px;">
              <i data-lucide="shield" style="width: 28px; height: 28px; color: #1E3A5F;"></i> GEICG
            </div>
          <?php endif; ?>
        </div>

        <!-- TITRE -->
        <div class="title-login">
          <h3>Nouveau mot de passe</h3>
          <p>Choisissez un nouveau mot de passe sécurisé pour réinitialiser votre accès.</p>
        </div>

        <!-- MESSAGES FLASH -->
        <?php if (!empty($_SESSION['flash_error'])): ?>
          <div class="alert alert-danger" style="width: 100%; margin-bottom: 20px; font-size: 13px; border-radius: 8px; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; padding: 12px; font-weight: 600; text-align: center; box-sizing: border-box;">
            ⚠️ <?= htmlspecialchars($_SESSION['flash_error']); ?>
          </div>
          <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <!-- FORMULAIRE -->
        <div class="input-wrapper">
          <form method="POST" action="<?= RACINE ?>user/reset_password">
            <?= Validator::csrfField() ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

            <div class="form-group-login">
              <label for="password">Nouveau mot de passe</label>
              <div class="input-icon-container">
                <span class="input-icon"><?= Validator::icon('lock'); ?></span>
                <input type="password" class="form-control" id="password" name="password" placeholder="Minimum 6 caractères" required autofocus minlength="6">
              </div>
            </div>

            <div class="form-group-login">
              <label for="confirm_password">Confirmer le mot de passe</label>
              <div class="input-icon-container">
                <span class="input-icon"><?= Validator::icon('check-circle'); ?></span>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Répéter le mot de passe" required minlength="6">
              </div>
            </div>

            <div style="margin-bottom: 20px; width: 100%;">
              <button type="submit" class="btn-login-submit">
                <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                <span>Enregistrer le nouveau mot de passe</span>
              </button>
            </div>

            <div style="text-align: center; width: 100%;">
              <a href="<?= RACINE ?>user/connexion" class="back-to-login">
                &larr; Retour à la page de connexion
              </a>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>

</div>
