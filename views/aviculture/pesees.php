<?php
require_once __DIR__ . '/../../public/inc/header.php';
$produits = $produits ?? [];
$categories = $categories ?? [];
?>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      
      <!-- Page Header -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">Registre des Pesées & Étiquetage OVOLIA</h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Saisie du poids net réel, détermination automatique de la tranche et génération des étiquettes</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
          <a href="<?= RACINE ?>aviculture/categories_poids" class="btn btn-secondary" style="background: #64748B; border-color: #64748B; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 16px; color: #fff;">
            <i data-lucide="scale" style="width: 18px; height: 18px;"></i> Grille des Poids
          </a>
          <button id="btnOpenPeseeModal" data-bs-toggle="modal" data-bs-target="#modalPesee" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Nouvelle Pesée
          </button>
        </div>
      </div>

      <!-- Card Table Historique Pesées -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; max-width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #F1F5F9;">
          <h2 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="list" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Historique des Pesées Enregistrées
          </h2>
        </div>

        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
          <table id="tablePesees" class="table display nowrap" style="width:100%; max-width:100%; border-collapse: collapse;">
            <thead>
              <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                <th style="padding: 12px;">Code Étiquette</th>
                <th style="padding: 12px;">Produit</th>
                <th style="padding: 12px;">Catégorie</th>
                <th style="padding: 12px;">Poids Net Réel</th>
                <th style="padding: 12px;">Prix Vente</th>
                <th style="padding: 12px;">N° Lot</th>
                <th style="padding: 12px;">Date Pesée</th>
                <th style="padding: 12px;">Agent</th>
                <th style="padding: 12px;">Statut</th>
                <th style="padding: 12px; text-align: center;">Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>

<!-- ========================================================================= -->
<!-- MODAL SAISIE NOUVELLE PESÉE                                               -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalPesee" tabindex="-1" aria-labelledby="modalPeseeLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 560px; margin: auto;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.25);">
      <div class="modal-header" style="background: #1E3A5F; color: #fff; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" id="modalPeseeLabel" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="scale" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Saisie Pesée & Génération Étiquette
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      
      <form id="formPesee">
        <div class="modal-body" style="padding: 20px;">
          <!-- Produit -->
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Sélectionner le Produit *</label>
            <select name="produit_code" id="selectProduit" class="form-select" style="border-radius: 8px; height: 42px; width: 100%; padding: 0 12px;" required>
              <?php if (empty($produits)): ?>
                <option value="PROD-POULET-FRAIS">Poulet entier frais pr&ecirc;t &agrave; cuire (kg)</option>
                <option value="PROD-PINTADE-FRAIS">Pintade enti&egrave;re fra&icirc;che (kg)</option>
              <?php else: ?>
                <option value="">-- Choisir un produit avicole --</option>
                <?php foreach ($produits as $p): ?>
                  <option value="<?= htmlspecialchars($p['code_produit_aviculture']) ?>">
                    <?= htmlspecialchars($p['libelle_produit']) ?> (<?= htmlspecialchars($p['unite_mesure']) ?>)
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Poids Net Réel Mesuré -->
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Poids Net Réel Mesuré (en kg) *</label>
            <div class="input-group">
              <input type="number" step="0.001" min="0.100" max="10.000" name="poids_net_reel" id="inputPoidsNet" class="form-control" style="border-radius: 8px 0 0 8px; height: 44px; font-size: 20px; font-weight: 800; color: #047857;" placeholder="Ex: 1.345" required>
              <span class="input-group-text" style="border-radius: 0 8px 8px 0; background: #F1F5F9; font-weight: 700; color: #334155;">kg</span>
            </div>
            <small style="color: #64748B; font-size: 12px; margin-top: 4px; display: block;">Saisir le poids net exact indiqué sur la balance.</small>
          </div>

          <!-- Aperçu en temps réel de la catégorie et du prix -->
          <div style="background: #F0FDF4; border: 2px dashed #059669; border-radius: 10px; padding: 14px; text-align: center; margin-top: 14px;" id="previewContainer">
            <div style="text-transform: uppercase; font-size: 11px; font-weight: 800; color: #059669;">Détection Automatique de Catégorie</div>
            <div id="previewCategory" style="font-size: 13px; font-weight: 800; padding: 4px 14px; border-radius: 20px; background: #0F172A; color: #FFFFFF; display: inline-block; margin-top: 6px;">-- En attente de saisie --</div>
            <div id="previewWeight" style="font-size: 28px; font-weight: 900; color: #047857; margin-top: 4px;">0,000 kg</div>
            <div id="previewPrice" style="font-weight: 800; color: #DC2626; font-size: 18px; margin-top: 2px;">0 FCFA</div>
          </div>

          <div class="row g-3" style="margin-top: 8px;">
            <div class="col-md-6">
              <label style="font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 4px; display: block;">Numéro de Lot</label>
              <input type="text" name="numero_lot" class="form-control" style="border-radius: 8px; height: 38px;" value="LOT-<?= date('Ymd') ?>">
            </div>
            <div class="col-md-6">
              <label style="font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 4px; display: block;">DLC (Limite Consommation)</label>
              <input type="date" name="date_limite_consommation" class="form-control" style="border-radius: 8px; height: 38px;" value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
            </div>
          </div>
        </div>

        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success" id="btnSubmitPesee" style="background: #059669; border-color: #059669; border-radius: 8px; font-weight: 700; padding: 8px 18px;">
            🖨️ Enregistrer & Générer Étiquette
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal d'affichage de l'étiquette pour impression rapide -->
<div class="modal fade" id="modalEtiquettePrint" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #059669; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px;">🏷️ Étiquette Produit OVOLIA</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0" id="printFrameContainer" style="height: 520px;">
        <iframe id="iframeEtiquette" style="width: 100%; height: 100%; border: none;"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
window.CATEGORIES_POIDS = <?= json_encode($categories) ?>;
</script>
<script src="<?= RACINE ?>public/assets/js/modules/aviculture.js?v=<?= time() ?>"></script>

<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
