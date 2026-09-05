
<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$item = $item ?? [];
$souscription = $souscription ?? [];
$nomClient = trim($souscription['nom_client'] ?? '');
$montant = (float)($item['montant_cautisation'] ?? 0);
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <!-- EN-TÊTE DU REÇU DE COTISATION -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Reçu de Cotisation : <code style="font-weight: 800; color: #047857; font-size: 20px; background: #ECFDF5; padding: 3px 10px; border-radius: 6px;"><?= htmlspecialchars($item['code_cautisation_client'] ?? '-') ?></code></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Versement encaissé le <strong><?= !empty($item['date_cautisation']) ? date('d/m/Y', strtotime($item['date_cautisation'])) : '-' ?></strong></p>
        </div>
        <div style="display: flex; gap: 12px;">
<a href="<?= RACINE ?>cotisation/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
          <i data-lucide="list" style="width: 16px; height: 16px;"></i> Liste des Cotisations
        </a>
        <a href="<?= RACINE ?>cotisation/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i> Modifier Reçu
          </a>
        </div>
      </div>

      <!-- CARTE SYNTHÈSE DU PAIEMENT -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 20px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="receipt" style="width: 18px; height: 18px;"></i> Détails du Règlement Client
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Client Souscripteur</span>
            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($nomClient ?: 'Client Non Renseigné') ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Tél : <?= htmlspecialchars($souscription['telephone_client'] ?? '-') ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Pack Produit</span>
            <div style="font-size: 17px; font-weight: 800; color: #1E3A5F; margin-top: 4px;"><?= htmlspecialchars($souscription['libelle_pack'] ?? 'Pack') ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Réf Contrat : <code><?= htmlspecialchars($item['souscription_code'] ?? '-') ?></code></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Montant Versé</span>
            <div style="font-size: 20px; font-weight: 800; color: #047857; margin-top: 4px;"><?= number_format($montant, 0, ',', ' ') ?> FCFA</div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Jours Régularisés : <strong style="color: #1E3A5F;">+<?= (int)($item['nombre_jour_paye'] ?? 1) ?> j</strong></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Mode & Réf Transaction</span>
            <div style="font-size: 15px; font-weight: 800; color: #0F172A; margin-top: 4px; text-transform: uppercase;"><?= htmlspecialchars($item['mode_paiement'] ?? 'Espèces') ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Réf : <code><?= htmlspecialchars($item['reference_paiement'] ?? 'Aucune') ?></code></div>
          </div>
        </div>

        <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
          <div>
            <span style="font-size: 12px; color: #64748B;">Commercial Encaisseur :</span>
            <strong style="color: #0F172A; font-size: 13px; margin-left: 4px;"><?= htmlspecialchars(trim(($item['nom_user'] ?? '') . ' ' . ($item['prenom_user'] ?? ''))) ?></strong>
          </div>
          <div>
            <span class="badge" style="background:#DCFCE7; color:#15803D; padding:6px 14px; border-radius:10px; font-weight:800; font-size:12px;">Cotisation Validée</span>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>
<script>$(document).ready(function() { if (window.lucide) lucide.createIcons(); });</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
