<?php
require_once __DIR__ . '/../../public/inc/header.php';
?>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>

    <div class="content-wrapper">
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <div>
          <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #1E293B; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="layers" style="color: #2563EB;"></i> Catégories de vêtements & linge
          </h1>
          <p class="page-subtitle" style="margin: 4px 0 0; color: #64748B; font-size: 13px;">Gestion et classification des types de vêtements et de linge</p>
        </div>
        <a href="<?= RACINE ?>categorie/formulaire" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 6px; font-weight: 700;">
          <i data-lucide="plus" style="width: 16px; height: 16px;"></i> Ajouter une catégorie
        </a>
      </div>

      <div class="card" style="border-radius: 14px; border: 1px solid #E2E8F0; padding: 20px; background: #FFFFFF;">
         <div class="mobile-list-container"></div>
         <div class="table-responsive-mobile">
             <table class="table" id="dataTable" style="width: 100%;">
                 <thead>
                  <tr>
                    <th style="width: 40px;">N°</th>
                    <th style="width: 60px; text-align: center;">Icône</th>
                    <th>Code</th>
                    <th>Libellé</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th style="text-align: center; width: 120px;">Actions</th>
                  </tr>
               </thead>
               <tbody></tbody>
            </table>
          </div>
       </div>

      <script src="<?= RACINE ?>public/json/mobile-list.js"></script>
      <script src="<?= RACINE ?>public/json/entities/categories.js?v=6"></script>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
