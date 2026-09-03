<?php
if (!headers_sent()) {
    http_response_code(403);
}
require_once __DIR__ . '/../../public/inc/header.php';
$message = $message ?? "Vous ne disposez pas des privilèges suffisants pour accéder à ce module ou exécuter cette action.";
$permissionCode = $permissionCode ?? '';
$auth = $_SESSION[USERS_AUTH] ?? [];
?>
<div class="app-layout">
  <?php if (isset($auth['id_user'])): ?>
    <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <?php endif; ?>
  
  <main class="main-content" style="<?= !isset($auth['id_user']) ? 'margin-left: 0; width: 100%;' : '' ?>">
    <?php if (isset($auth['id_user'])): ?>
      <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <?php endif; ?>

    <div class="content-wrapper" style="padding: 40px 24px; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
      <div class="card" style="background: #FFFFFF; border-radius: 16px; padding: 40px; border: 1px solid #E2E8F0; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01); max-width: 600px; width: 100%; text-align: center;">
        
        <!-- Icone de Sécurité -->
        <div style="width: 80px; height: 80px; border-radius: 50%; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto;">
          <i data-lucide="shield-alert" style="width: 42px; height: 42px;"></i>
        </div>

        <span style="display: inline-block; background: #FEF2F2; color: #991B1B; font-weight: 800; font-size: 12px; letter-spacing: 1px; padding: 4px 14px; border-radius: 20px; text-transform: uppercase; margin-bottom: 12px; border: 1px solid #FECACA;">
          Erreur 403 &bull; Accès Non Autorisé
        </span>

        <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; margin: 0 0 12px 0;">
          Privilèges Insuffisants
        </h1>

        <p style="color: #64748B; font-size: 15px; line-height: 1.6; margin: 0 0 24px 0;">
          <?= htmlspecialchars($message) ?>
        </p>

        <?php if (!empty($permissionCode)): ?>
          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
            <div style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; margin-bottom: 4px;">Privilège Système Requis :</div>
            <code style="font-size: 14px; font-weight: 800; color: #DC2626; background: #FEF2F2; padding: 2px 8px; border-radius: 4px; border: 1px solid #FECACA; display: inline-block;">
              <?= htmlspecialchars($permissionCode) ?>
            </code>
          </div>
        <?php endif; ?>

        <?php if (isset($auth['role_code'])): ?>
          <div style="font-size: 13px; color: #64748B; margin-bottom: 28px;">
            Connecté en tant que : <strong style="color: #1E3A5F;"><?= htmlspecialchars($auth['nom'] ?? 'Utilisateur') ?></strong> (Rôle : <code><?= htmlspecialchars($auth['role_code']) ?></code>)
          </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
          <button type="button" onclick="window.history.back()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; padding: 11px 22px; border-radius: 8px;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Page précédente
          </button>
          
          <a href="<?= RACINE ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; padding: 11px 22px; border-radius: 8px;">
            <i data-lucide="home" style="width: 18px; height: 18px;"></i> Tableau de bord
          </a>
        </div>

        <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid #F1F5F9; font-size: 12px; color: #94A3B8;">
          Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administrateur système pour ajuster vos droits d'accès.
        </div>

      </div>
    </div>
  </main>
</div>
<script>$(document).ready(function() { if (window.lucide) lucide.createIcons(); });</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
