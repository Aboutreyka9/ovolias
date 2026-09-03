<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$item = $item ?? [];
$packArticles = $packArticles ?? [];
$libellePack = $item['libelle_pack'] ?? 'Pack Produit';
$montantPack = (int)($item['montant_pack'] ?? 0);
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <!-- EN-TÊTE DE LA FICHE PACK -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Fiche Pack : <?= htmlspecialchars($libellePack) ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Code Pack : <code style="font-weight: 800; color: #1E3A5F; font-size: 13px; background: #F1F5F9; padding: 2px 8px; border-radius: 4px;"><?= htmlspecialchars($item['code_pack'] ?? '-') ?></code> &bull; Catégorie : <strong><?= htmlspecialchars($item['libelle_categorie_pack'] ?? '-') ?></strong></p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>pack/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour aux packs
          </a>
          <a href="<?= RACINE ?>pack/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i> Modifier le Pack
          </a>
        </div>
      </div>

      <!-- CARTE 1 : INFORMATIONS DU PACK -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="boxes" style="width: 18px; height: 18px;"></i> Informations du Pack
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Désignation Pack</span>
            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($libellePack) ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Montant Total</span>
            <div style="font-size: 20px; font-weight: 800; color: #1E3A5F; margin-top: 4px;"><?= number_format($montantPack, 0, ',', ' ') ?> FCFA</div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Session</span>
            <div style="font-size: 15px; font-weight: 700; color: #334155; margin-top: 4px;"><?= htmlspecialchars($item['libelle_session'] ?? '-') ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Zone</span>
            <div style="font-size: 15px; font-weight: 700; color: #334155; margin-top: 4px;"><?= htmlspecialchars($item['libelle_zone'] ?? '-') ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Catégorie</span>
            <div style="font-size: 15px; font-weight: 700; color: #334155; margin-top: 4px;"><?= htmlspecialchars($item['libelle_categorie_pack'] ?? '-') ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Statut d'Offre</span>
            <div style="margin-top: 6px;">
              <?php if (($item['statut_pack'] ?? '') === 'actif'): ?>
                <span class="badge" style="background:#DCFCE7; color:#15803D; padding:6px 14px; border-radius:10px; font-weight:800; font-size:12px;">Actif (Ouvert)</span>
              <?php else: ?>
                <span class="badge" style="background:#FEF2F2; color:#B91C1C; padding:6px 14px; border-radius:10px; font-weight:800; font-size:12px;">Inactif</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- CARTE 2 : COMPOSITION EN ARTICLES -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 12px;">
          <i data-lucide="shopping-bag" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Composition en Articles de ce Pack
        </h3>

        <?php if (empty($packArticles)): ?>
          <p style="color: #94A3B8; text-align: center; padding: 20px 0; font-style: italic;">Aucun article individuel associé à ce pack.</p>
        <?php else: ?>
          <div style="width: 100%; overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
              <thead>
                <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                  <th style="padding: 10px 12px;">Article / Produit</th>
                  <th style="padding: 10px 12px; text-align: center;">Quantité Incluse</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($packArticles as $pa): ?>
                  <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 10px 12px; font-weight: 700; color: #0F172A;"><?= htmlspecialchars($pa['libelle_article'] ?? $pa['article_code']) ?></td>
                    <td style="padding: 10px 12px; text-align: center;"><span class="badge" style="background:#EFF6FF; color:#1E3A5F; padding:4px 10px; border-radius:8px; font-weight:800;">x<?= (int)($pa['quantite_article'] ?? 1) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>
<script>$(document).ready(function() { if (window.lucide) lucide.createIcons(); });</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
