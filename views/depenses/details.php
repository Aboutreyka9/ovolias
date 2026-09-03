<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$item = $item ?? [];
$typeDepense = $typeDepense ?? [];
$montant = (float)($item['montant_depense'] ?? 0);
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <!-- EN-TÊTE DE LA FICHE DÉPENSE -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Fiche Dépense : <code style="font-weight: 800; color: #DC2626; font-size: 20px; background: #FEF2F2; padding: 3px 10px; border-radius: 6px;"><?= htmlspecialchars($item['code_depense'] ?? '-') ?></code></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Frais engagé le <strong><?= !empty($item['created_at_depense']) ? date('d/m/Y H:i', strtotime($item['created_at_depense'])) : (!empty($item['date_depense']) ? date('d/m/Y', strtotime($item['date_depense'])) : '-') ?></strong></p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>depense/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour aux dépenses
          </a>
          <a href="<?= RACINE ?>depense/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i> Modifier Dépense
          </a>
        </div>
      </div>

      <!-- CARTE SYNTHÈSE DE LA DÉPENSE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 20px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="arrow-up-right" style="width: 18px; height: 18px; color: #DC2626;"></i> Détails de la Sortie de Caisse
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Catégorie de Dépense</span>
            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($typeDepense['libelle_type_depense'] ?? ($item['libelle_type_depense'] ?? '-')) ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Code Type : <code><?= htmlspecialchars($item['type_depense_code'] ?? '-') ?></code></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Montant Engagé</span>
            <div style="font-size: 22px; font-weight: 800; color: #DC2626; margin-top: 4px;"><?= number_format($montant, 0, ',', ' ') ?> FCFA</div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Mode de Règlement</span>
            <div style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin-top: 4px; text-transform: uppercase;"><?= htmlspecialchars($item['mode_reglement'] ?? 'Espèces') ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Statut Dépense</span>
            <div style="margin-top: 6px;">
              <span class="badge" style="background:#DCFCE7; color:#15803D; padding:6px 14px; border-radius:10px; font-weight:800; font-size:12px;">Comptabilisée</span>
            </div>
          </div>
        </div>

        <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid #F1F5F9;">
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; display: block; margin-bottom: 6px;">Description / Motif du Frais</span>
          <div style="font-size: 14px; color: #334155; line-height: 1.6; background: #F8FAFC; padding: 12px 16px; border-radius: 8px; border: 1px solid #E2E8F0;">
            <?= nl2br(htmlspecialchars($item['description_depense'] ?? ($item['motif_depense'] ?? 'Aucun motif renseigné'))) ?>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>
<script>$(document).ready(function() { if (window.lucide) lucide.createIcons(); });</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
