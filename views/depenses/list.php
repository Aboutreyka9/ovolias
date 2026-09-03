<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">Journal des Dépenses d'Exploitation</h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Enregistrement des charges et sorties de caisse</p>
        </div>
        <a href="<?= RACINE ?>depense/formulaire" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
          <i data-lucide="minus-circle" style="width: 18px; height: 18px;"></i> Nouvelle Dépense
        </a>
      </div>
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; max-width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
          <table id="table-depenses" class="table display nowrap" style="width:100%; max-width:100%; border-collapse: collapse;">
            <thead>
              <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                <th style="padding: 12px;">Code</th>
                <th style="padding: 12px;">Date</th>
                <th style="padding: 12px;">Catégorie Dépense</th>
                <th style="padding: 12px;">Motif / Description</th>
                <th style="padding: 12px;">Montant Engagé</th>
                <th style="padding: 12px;">Auteur</th>
                <th style="padding: 12px; text-align: center;">Statut</th>
                <th style="padding: 12px; text-align: right;">Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="<?= RACINE ?>public/assets/js/modules/depenses.js?v=1.0"></script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
