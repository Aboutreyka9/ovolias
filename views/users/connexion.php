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
    padding: 20px;
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
    align-items: center;
    padding: 36px 32px;
    border-radius: 12px;
    box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #E2E8F0;
    box-sizing: border-box;
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
    font-size: 26px;
    font-weight: 800;
    margin: 0;
    letter-spacing: 0.5px;
  }

  .title-login p {
    color: #64748B;
    font-weight: 600;
    font-size: 14px;
    margin: 4px 0 0 0;
  }

  .input-wrapper {
    width: 100%;
  }

  .form-group-login {
    margin-bottom: 18px;
    width: 100%;
  }

  .form-group-login label {
    display: block;
    font-weight: 700;
    font-size: 13px;
    color: #334155;
    margin-bottom: 6px;
  }

  .input-icon-container {
    position: relative;
    width: 100%;
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
  }

  .input-icon-container .form-control:focus {
    border-color: #1E3A5F;
    box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
  }

  .password-toggle-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #64748B;
    padding: 4px;
    display: flex;
    align-items: center;
  }

  .password-toggle-btn:hover {
    color: #1E3A5F;
  }

  .checkbox-container {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 22px;
    font-size: 13px;
    color: #475569;
    user-select: none;
  }

  .checkbox-container input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #1E3A5F;
  }

  .btn-login-submit {
    width: 100%;
    background: #1E3A5F;
    border: 1px solid #1E3A5F;
    color: #FFFFFF;
    font-weight: 700;
    font-size: 15px;
    padding: 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .btn-login-submit:hover {
    background: #152C48;
    border-color: #152C48;
  }

  .divider-login {
    width: 100%;
    height: 1px;
    background-color: #E2E8F0;
    margin: 24px 0 16px 0;
    border: none;
  }

  .inscrit-login {
    font-size: 12px;
    font-weight: 600;
    color: #64748B;
    text-align: center;
    margin: 0;
  }

  @media (max-width: 768px) {
    .content-left {
      display: none;
    }

    .content-right {
      width: 100%;
      padding: 16px;
    }

    .writer-login {
      padding: 28px 20px;
    }
  }
</style>

<div class="login-wrapper">

  <!-- CÔTÉ GAUCHE : IMAGE COVER -->
  <div class="content-left">
    <div class="image-wrapper"></div>
  </div>

  <!-- CÔTÉ DROIT : FORMULAIRE DE CONNEXION -->
  <div class="content-right">
    <div class="wrap-content">
      <div class="writer-login">

        <!-- LOGO ÉTABLISSEMENT -->
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
          <h3>Se connecter</h3>
          <p>à mon espace d'administration</p>
        </div>

        <!-- MESSAGES FLASH D'ACTIVATION / SÉCURITÉ -->
        <?php if (!empty($_SESSION['flash_success'])): ?>
          <div class="alert alert-success" style="width: 100%; margin-bottom: 16px; font-size: 13px; border-radius: 8px; background: #DCFCE7; color: #15803D; border: 1px solid #86EFAC; padding: 12px; font-weight: 600; text-align: center;">
            ✅ <?= htmlspecialchars($_SESSION['flash_success']); ?>
          </div>
          <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
          <div class="alert alert-danger" style="width: 100%; margin-bottom: 16px; font-size: 13px; border-radius: 8px; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; padding: 12px; font-weight: 600; text-align: center;">
            ⚠️ <?= htmlspecialchars($_SESSION['flash_error']); ?>
          </div>
          <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <!-- ZONE DE NOTIFICATION JS COMPATIBLE -->
        <div class="notification alert alert-danger d-none" style="width: 100%; margin-bottom: 16px; font-size: 13px; border-radius: 8px;"></div>

        <!-- FORMULAIRE -->
        <div class="input-wrapper">
          <form class="formConnexion" method="POST">
            <?= Validator::csrfField() ?>

            <!-- BOUTON GOOGLE AUTH -->
            <div style="margin-bottom: 18px;">
              <a href="<?= RACINE ?>auth/google" id="submit_google" class="btn-google-auth" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; padding: 11px 16px; border: 1px solid #E2E8F0; color: #334155; background: #FFFFFF; border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.05); box-sizing: border-box;">
                <svg width="20px" height="20px" class="google-icon" viewBox="0 0 48 48" aria-hidden="true">
                  <path fill="#EA4335" d="M24 9.5c3.4 0 6.4 1.2 8.8 3.2l6.6-6.6C35.6 2.5 30.2 0 24 0 14.6 0 6.6 5.8 2.8 14.1l7.7 6C12.3 13.2 17.6 9.5 24 9.5z" />
                  <path fill="#4285F4" d="M46.1 24.5c0-1.7-.2-3.4-.5-5H24v9.5h12.4c-.5 2.6-2 4.8-4.2 6.3l6.5 5c3.8-3.5 7.4-8.7 7.4-15.8z" />
                  <path fill="#FBBC05" d="M10.5 28.1c-1-2.9-1-6.1 0-9l-7.7-6C.9 17 .9 23 2.8 27.9l7.7-5.8z" />
                  <path fill="#34A853" d="M24 48c6.5 0 12-2.1 16-5.7l-6.5-5c-2 1.4-4.6 2.2-9.5 2.2-6.4 0-11.7-3.7-13.6-8.6l-7.7 5.8C6.6 42.2 14.6 48 24 48z" />
                </svg>
                <span>Se connecter avec Google</span>
              </a>
            </div>

            <!-- SÉPARATEUR -->
            <div style="display: flex; align-items: center; margin: 20px 0; color: #94A3B8; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
              <div style="flex: 1; height: 1px; background-color: #E2E8F0;"></div>
              <span style="padding: 0 10px;">ou identifiants</span>
              <div style="flex: 1; height: 1px; background-color: #E2E8F0;"></div>
            </div>

            <!-- LOGIN : EMAIL / TÉLÉPHONE -->
            <div class="form-group-login">
              <label for="login">Adresse email ou Téléphone</label>
              <div class="input-icon-container">
                <span class="input-icon"><?= Validator::icon('user'); ?></span>
                <input type="text" class="form-control" id="login" name="login" placeholder="Ex: admin@gmail.com ou 0708091011" required autofocus>
              </div>
            </div>

            <!-- MOT DE PASSE -->
            <div class="form-group-login">
              <label for="password">Mot de passe</label>
              <div class="input-icon-container">
                <span class="input-icon"><?= Validator::icon('lock'); ?></span>
                <input type="password" class="form-control password" id="password" name="password" placeholder="Mot de passe" required>
                <button type="button" class="password-toggle-btn" id="togglePassword" aria-label="Afficher le mot de passe">
                  <i data-lucide="eye" id="eyeIcon"></i>
                </button>
              </div>
            </div>

            <!-- AFFICHER MOT DE PASSE & MOT DE PASSE OUBLIÉ -->
            <div class="checkbox-container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
              <label for="show-password" style="cursor: pointer; display: flex; align-items: center; gap: 6px; margin: 0;">
                <input type="checkbox" id="show-password">
                <span>Afficher mot de passe</span>
              </label>

              <a href="<?= RACINE ?>user/forgot_password" class="forgot-password-link" style="color: #2563EB; font-size: 13px; font-weight: 600; text-decoration: none; transition: color 0.2s;">
                Mot de passe oublié ?
              </a>
            </div>

            <!-- BOUTON DE SOUMISSION -->
            <div style="margin-bottom: 16px;">
              <button type="submit" class="btn btn-primary btn-block btn_actions btnConnexion btn-login-submit">
                <span class="btn-text" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                  <i data-lucide="log-in" style="width: 18px; height: 18px;"></i>
                  Se connecter
                </span>
              </button>
            </div>

            <hr class="divider-login">

            <p class="inscrit-login">
              Copyright &copy; <?= date('Y') ?> SMART-CODES / GEICG. Tous droits réservés.
            </p>
          </form>
        </div>

      </div>
    </div>
  </div>

</div>
