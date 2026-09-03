<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>

    <div class="content-wrapper">
      <div class="page-header">
        <div>
          <h1>Mon profil</h1>
          <p class="page-subtitle">Informations de votre compte</p>
        </div>
      </div>

      <div class="detail-card">
        <div class="detail-card-header"><h2>Profil</h2></div>
        <div class="detail-card-body">
          <div class="info-list">
            <div class="info-item">
              <span class="info-label">Nom</span>
              <span class="info-value"><?= htmlspecialchars($_SESSION[USERS_AUTH]['nom'] ?? '-') ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Email</span>
              <span class="info-value"><?= htmlspecialchars($_SESSION[USERS_AUTH]['email'] ?? '-') ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Téléphone</span>
              <span class="info-value"><?= htmlspecialchars($_SESSION[USERS_AUTH]['tel'] ?? '-') ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="form-actions" style="margin-top: 20px;">
        <a href="<?= RACINE ?>user/editPassword" class="btn btn-primary"><i data-lucide="lock"></i> Modifier le mot de passe</a>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
