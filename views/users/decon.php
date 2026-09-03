<?php
require_once __DIR__ . '/../../public/inc/header.php';
?>

<div class="app-layout auth-layout">
  <main class="auth-content">
    <div class="auth-card">
      <div class="auth-header">
        <div class="logo"> <?= LOGO ?> </div>
        <h1>Déconnexion</h1>
        <p>Vous êtes sur le point de vous déconnecter</p>
      </div>

      <div class="auth-body">
        <form class="formDecon" method="POST" action="<?= RACINE ?>user/decon">
          <?= Validator::csrfField() ?>

          <div class="form-actions" style="justify-content: center; gap: 1rem;">
            <a href="<?= RACINE ?>" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary btn_actions btnDecon" style="background: #1E3A5F; border-color: #1E3A5F;">
              <span class="btn-text">
                <i data-lucide="log-out"></i>
                Se déconnecter
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>

<script src="<?= RACINE ?>public/json/auth.js"></script>
<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
