<?php
if (!headers_sent()) {
    http_response_code(404);
}
require_once __DIR__ . '/../../public/inc/header.php';
$message = $message ?? "La page ou la ressource que vous recherchez n'existe pas ou a été déplacée.";
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
      <div class="card" style="background: #FFFFFF; border-radius: 16px; padding: 40px; border: 1px solid #E2E8F0; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01); max-width: 580px; width: 100%; text-align: center;">
        
        <!-- Icone 404 -->
        <div style="width: 80px; height: 80px; border-radius: 50%; background: #EFF6FF; color: #1E3A5F; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto;">
          <i data-lucide="compass" style="width: 42px; height: 42px;"></i>
        </div>

        <span style="display: inline-block; background: #EFF6FF; color: #1E3A5F; font-weight: 800; font-size: 12px; letter-spacing: 1px; padding: 4px 14px; border-radius: 20px; text-transform: uppercase; margin-bottom: 12px; border: 1px solid #BFDBFE;">
          Erreur 404 &bull; Page Introuvable
        </span>

        <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; margin: 0 0 12px 0;">
          Ressource Non Trouvée
        </h1>

        <p style="color: #64748B; font-size: 15px; line-height: 1.6; margin: 0 0 28px 0;">
          <?= htmlspecialchars($message) ?>
        </p>

        <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
          <button type="button" onclick="window.history.back()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; padding: 11px 22px; border-radius: 8px;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Page précédente
          </button>
          
          <a href="<?= RACINE ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; padding: 11px 22px; border-radius: 8px;">
            <i data-lucide="home" style="width: 18px; height: 18px;"></i> Retour à l'accueil
          </a>
        </div>

        <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid #F1F5F9; font-size: 12px; color: #94A3B8;">
          GEICG &bull; Système de Gestion Académique & Sécurité
        </div>

      </div>
    </div>
  </main>
</div>
<script>$(document).ready(function() { if (window.lucide) lucide.createIcons(); });</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
