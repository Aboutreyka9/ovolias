<?php 
require_once __DIR__ . '/../../public/inc/header.php'; 
$categories = $categories ?? [];
$grilles = $grilles ?? [];
?>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      
      <!-- Page Header à l'image du modèle Année Académique -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">Grille Tarifaire & Catégories de Poids OVOLIA</h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Barème officiel par tranche de poids net pour Poulets entiers frais & Pintades</p>
        </div>
        <a href="<?= RACINE ?>aviculture/pesees" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
          <i data-lucide="qr-code" style="width: 18px; height: 18px;"></i> Registre des Pesées
        </a>
      </div>

      <!-- Grille des 6 Catégories de Poids Standard en Cartes Structurées -->
      <div style="margin-bottom: 24px;">
        <h2 style="font-size: 16px; font-weight: 700; color: #1E293B; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="scale" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Les 6 Catégories de Référence OVOLIA
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
          <?php 
          $accents = [
              'CATP-ESSENTIEL' => '#10B981',
              'CATP-CLASSIQUE' => '#059669',
              'CATP-GRAND'     => '#0284C7',
              'CATP-EXTRA'     => '#2563EB',
              'CATP-SIGNATURE' => '#7C3AED',
              'CATP-PRESTIGE'  => '#DC2626'
          ];
          foreach ($categories as $cat): 
              $color = $accents[$cat['code_categorie_poids']] ?? '#059669';
          ?>
          <div style="background: #FFFFFF; border-radius: 10px; padding: 18px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: <?= $color ?>;"></div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
              <span style="font-weight: 800; font-size: 15px; color: #0F172A;"><?= htmlspecialchars($cat['libelle_categorie_poids']) ?></span>
              <code style="font-weight:700; color:<?= $color ?>; background:<?= $color ?>12; padding:2px 6px; border-radius:4px; font-size: 11px;">
                <?= htmlspecialchars($cat['code_categorie_poids']) ?>
              </code>
            </div>
            <div style="font-size: 13px; font-weight: 700; color: #047857; background: #ECFDF5; padding: 4px 8px; border-radius: 6px; display: inline-block; margin-bottom: 12px;">
              ⚖️ <?= number_format($cat['poids_min'], 2, ',', ' ') ?> kg à <?= number_format($cat['poids_max'], 2, ',', ' ') ?> kg
            </div>
            <div style="display: flex; justify-content: space-between; align-items: baseline;">
              <span style="color: #64748B; font-size: 12px; font-weight: 600;">Tarif Vente</span>
              <span style="font-size: 20px; font-weight: 900; color: <?= $color ?>;">
                <?= number_format($cat['prix_vente_defaut'], 0, ',', ' ') ?> <small style="font-size: 12px; font-weight: 700;">FCFA</small>
              </span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Card Table Matrice Tarifaire (Exactement sur le modèle de table-annees) -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; max-width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; pb-2; border-bottom: 1px solid #F1F5F9;">
          <h2 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="layers" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Matrice Tarifaire Complète par Produit
          </h2>
          <span style="background: #EFF6FF; color: #1E3A5F; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
            Catalogue Produits OVOLIA
          </span>
        </div>

        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
          <table id="table-categories-poids" class="table display nowrap" style="width:100%; max-width:100%; border-collapse: collapse;">
            <thead>
              <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                <th style="padding: 12px;">Produit Avicole</th>
                <th style="padding: 12px;">Code Catégorie</th>
                <th style="padding: 12px;">Libellé Catégorie</th>
                <th style="padding: 12px;">Tranche Poids Net (Min - Max)</th>
                <th style="padding: 12px; text-align: right;">Tarif Vente Appliqué</th>
                <th style="padding: 12px; text-align: center;">Statut</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($grilles as $g): ?>
                <tr>
                  <td style="padding: 12px; font-weight: 700; color: #0F172A;">
                    <i data-lucide="feather" style="width: 14px; height: 14px; color: #047857; margin-right: 4px;"></i>
                    <?= htmlspecialchars($g['libelle_produit']) ?>
                  </td>
                  <td style="padding: 12px;">
                    <code style="font-weight:700; color:#334155; background:#F1F5F9; padding:2px 6px; border-radius:4px;">
                      <?= htmlspecialchars($g['code_categorie_poids']) ?>
                    </code>
                  </td>
                  <td style="padding: 12px; font-weight: 600; color: #334155;">
                    <?= htmlspecialchars($g['libelle_categorie_poids']) ?>
                  </td>
                  <td style="padding: 12px; color: #64748B;">
                    <?= number_format($g['poids_min'], 2, ',', ' ') ?> kg &mdash; <?= number_format($g['poids_max'], 2, ',', ' ') ?> kg
                  </td>
                  <td style="padding: 12px; text-align: right; font-weight: 800; color: #059669; font-size: 15px;">
                    <?= number_format($g['prix_vente'], 0, ',', ' ') ?> FCFA
                  </td>
                  <td style="padding: 12px; text-align: center;">
                    <span style="background: #DCFCE7; color: #166534; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 12px;">
                      Actif
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
$(document).ready(function() {
  $('#table-categories-poids').DataTable({
    processing: true,
    autoWidth: false,
    language: { url: '<?= RACINE ?>json/datatables-i18n-fr-FR.json' },
    drawCallback: function() { if (window.lucide) lucide.createIcons(); }
  });
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
