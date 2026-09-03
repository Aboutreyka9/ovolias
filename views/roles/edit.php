<?php
require_once __DIR__ . '/../../public/inc/header.php';
$role = isset($role) ? $role : [];
$allPermissions = isset($allPermissions) ? $allPermissions : [];
$assignedCodes = isset($assignedCodes) ? $assignedCodes : [];
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px;">
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;"><?= !empty($role['id']) ? 'Configuration du Rôle : ' . htmlspecialchars($role['libelle_role']) : 'Ajouter un Rôle & Profil' ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Définition des accès et des permissions granulaires du rôle</p>
        </div>
        <a href="<?= RACINE ?>role/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
          <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour aux rôles
        </a>
      </div>

      <form action="<?= RACINE ?>role/<?= !empty($role['id']) ? 'edit' : 'add' ?>" method="POST" style="width: 100%;">
        <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
        <?php if (!empty($role['id'])): ?>
          <input type="hidden" name="id" value="<?= $role['id'] ?>">
        <?php endif; ?>

        <!-- Informations générales -->
        <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px;">
          <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 8px;">
            <i data-lucide="shield" style="width: 18px; height: 18px;"></i> Informations du Rôle
          </h3>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
            <div class="form-group">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Libellé du Rôle <span style="color: #EF4444;">*</span></label>
              <input type="text" class="form-control" name="libelle_role" value="<?= htmlspecialchars($role['libelle_role'] ?? '') ?>" placeholder="Ex: Responsable Scolarité" required style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 600;">
            </div>

            <div class="form-group">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Code Système</label>
              <input type="text" class="form-control" name="code_role" value="<?= htmlspecialchars($role['code_role'] ?? '') ?>" placeholder="Ex: ROLE_SCOLARITE" <?= !empty($role['id']) ? 'readonly' : '' ?> style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: <?= !empty($role['id']) ? '#F1F5F9' : '#FFF' ?>; font-family: monospace; font-weight: 700;">
            </div>

            <div class="form-group">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Groupe / Département</label>
              <input type="text" class="form-control" name="groupe" value="<?= htmlspecialchars($role['groupe'] ?? 'Direction') ?>" placeholder="Ex: Direction Pédagogique, Finance..." style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #CBD5E1;">
            </div>

            <div class="form-group">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Description</label>
              <input type="text" class="form-control" name="description" value="<?= htmlspecialchars($role['description'] ?? '') ?>" placeholder="Ex: Gestion des inscriptions et élèves" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #CBD5E1;">
            </div>
          </div>
        </div>

        <!-- Section Permissions & Privilèges Granulaires avec Accordéons -->
        <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px;">
          
          <!-- En-tête global avec boutons d'actions groupées -->
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px; border-bottom: 2px solid #EFF6FF; padding-bottom: 14px;">
            <div>
              <h3 style="font-size: 16px; font-weight: 800; color: #1E3A5F; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="key" style="width: 20px; height: 20px; color: #1E3A5F;"></i> Permissions & Privilèges Granulaires
              </h3>
              <p style="color: #64748B; font-size: 12px; margin: 4px 0 0 0;">Cochez les droits et privilèges attribués à ce rôle</p>
            </div>
            
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
              <button type="button" class="btn btn-sm" onclick="checkAllGlobal(true)" style="background: #EFF6FF; color: #1E3A5F; border: 1px solid #BFDBFE; font-weight: 700; border-radius: 6px; padding: 7px 14px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                <i data-lucide="check-check" style="width: 15px; height: 15px;"></i> Tout cocher
              </button>
              <button type="button" class="btn btn-sm" onclick="checkAllGlobal(false)" style="background: #FFF; color: #64748B; border: 1px solid #CBD5E1; font-weight: 600; border-radius: 6px; padding: 7px 14px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                <i data-lucide="x" style="width: 15px; height: 15px;"></i> Tout décocher
              </button>
              <button type="button" class="btn btn-sm" onclick="toggleAllGroups(true)" style="background: #F8FAFC; color: #334155; border: 1px solid #CBD5E1; font-weight: 600; border-radius: 6px; padding: 7px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" title="Déplier tous les modules">
                <i data-lucide="chevrons-down" style="width: 15px; height: 15px;"></i> Déplier
              </button>
              <button type="button" class="btn btn-sm" onclick="toggleAllGroups(false)" style="background: #F8FAFC; color: #334155; border: 1px solid #CBD5E1; font-weight: 600; border-radius: 6px; padding: 7px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" title="Replier tous les modules">
                <i data-lucide="chevrons-up" style="width: 15px; height: 15px;"></i> Replier
              </button>
            </div>
          </div>

          <!-- Liste des Modules de Permissions -->
          <div style="display: flex; flex-direction: column; gap: 16px;">
            <?php 
            $grpIdx = 0;
            foreach ($allPermissions as $module => $perms): 
              $grpIdx++;
              $grpId = 'grp_' . $grpIdx;
              $totalCount = count($perms);
              $checkedCount = 0;
              foreach ($perms as $p) {
                if (in_array($p['code_permission'], $assignedCodes, true)) {
                  $checkedCount++;
                }
              }
            ?>
              <div class="permission-module-box" id="box-<?= $grpId ?>" style="border: 1px solid #E2E8F0; border-radius: 10px; overflow: hidden; background: #FFFFFF; transition: box-shadow 0.2s ease;">
                
                <!-- En-tête du Module (Clic pour réduire/ouvrir) -->
                <div class="module-header" onclick="toggleAccordion('<?= $grpId ?>')" 
                     style="background: #F8FAFC; padding: 12px 18px; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none;">
                  
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <!-- Icône Chevron pour Toggle Réduire / Ouvrir -->
                    <span id="chevron-wrap-<?= $grpId ?>" style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; transition: transform 0.25s ease;">
                      <i data-lucide="chevron-down" id="chevron-<?= $grpId ?>" style="width: 18px; height: 18px; color: #64748B;"></i>
                    </span>
                    <strong style="font-size: 13px; color: #1E293B; letter-spacing: 0.5px; text-transform: uppercase;">
                      <?= htmlspecialchars($module) ?>
                    </strong>
                    <!-- Badge compteur dynamique -->
                    <span id="badge-<?= $grpId ?>" style="font-size: 11px; font-weight: 700; color: #1E3A5F; background: #EFF6FF; border: 1px solid #BFDBFE; padding: 2px 10px; border-radius: 12px;">
                      <?= $checkedCount ?> / <?= $totalCount ?> cochées
                    </span>
                  </div>

                  <!-- Boutons spécifiques au module (Tout cocher / Tout décocher) -->
                  <div style="display: flex; gap: 6px;" onclick="event.stopPropagation();">
                    <button type="button" class="btn btn-sm" onclick="checkModulePerms('<?= $grpId ?>', true, event)" 
                            style="background: #EFF6FF; color: #1E3A5F; border: 1px solid #BFDBFE; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                      <i data-lucide="check" style="width: 13px; height: 13px; color: #15803D;"></i> Tout cocher
                    </button>
                    <button type="button" class="btn btn-sm" onclick="checkModulePerms('<?= $grpId ?>', false, event)" 
                            style="background: #FFFFFF; color: #64748B; border: 1px solid #CBD5E1; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                      <i data-lucide="x" style="width: 13px; height: 13px;"></i> Décocher
                    </button>
                  </div>
                </div>

                <!-- Corps du Module (Grille de checkboxes) -->
                <div class="module-body" id="body-<?= $grpId ?>" style="padding: 16px 18px; display: block; background: #FFFFFF;">
                  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px;">
                    <?php foreach ($perms as $p): ?>
                      <?php $isChecked = in_array($p['code_permission'], $assignedCodes, true); ?>
                      <label style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 1px solid #F1F5F9; border-radius: 8px; background: #FAFAFA; cursor: pointer; transition: all 0.15s ease;" 
                             onmouseover="this.style.background='#EFF6FF'; this.style.borderColor='#BFDBFE';" 
                             onmouseout="this.style.background='#FAFAFA'; this.style.borderColor='#F1F5F9';">
                        <input type="checkbox" name="permissions[]" class="perm-checkbox perm-group-<?= $grpId ?>" 
                               data-group="<?= $grpId ?>" value="<?= htmlspecialchars($p['code_permission']) ?>" 
                               <?= $isChecked ? 'checked' : '' ?> 
                               onchange="updateGroupBadge('<?= $grpId ?>', <?= $totalCount ?>)" 
                               style="width: 17px; height: 17px; accent-color: #1E3A5F; cursor: pointer;">
                        <span style="font-size: 13px; font-weight: 600; color: #0F172A; line-height: 1.3;">
                          <?= htmlspecialchars($p['libelle_permission']) ?>
                        </span>
                      </label>
                    <?php endforeach; ?>
                  </div>
                </div>

              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div style="display: flex; gap: 12px; padding: 20px 0;">
          <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 12px 28px; font-size: 14px;">
            Enregistrer le Rôle & Permissions
          </button>
          <a href="<?= RACINE ?>role/list" class="btn btn-secondary" style="font-weight: 600; border-radius: 8px; padding: 12px 24px;">Annuler</a>
        </div>
      </form>
    </div>
  </main>
</div>

<script>
$(document).ready(function() { 
  if (window.lucide) lucide.createIcons(); 
});

// Toggle ouvrir / réduire un module avec rotation du chevron
function toggleAccordion(grpId) {
  var $body = $('#body-' + grpId);
  var $wrap = $('#chevron-wrap-' + grpId);
  if ($body.is(':visible')) {
    $body.slideUp(180);
    $wrap.css('transform', 'rotate(-90deg)');
  } else {
    $body.slideDown(180);
    $wrap.css('transform', 'rotate(0deg)');
  }
}

// Déplier ou replier tous les modules
function toggleAllGroups(open) {
  if (open) {
    $('.module-body').slideDown(180);
    $('[id^="chevron-wrap-"]').css('transform', 'rotate(0deg)');
  } else {
    $('.module-body').slideUp(180);
    $('[id^="chevron-wrap-"]').css('transform', 'rotate(-90deg)');
  }
}

// Cocher ou décocher toutes les permissions d'un module spécifique
function checkModulePerms(grpId, checked, evt) {
  if (evt) evt.stopPropagation();
  $('.perm-group-' + grpId).prop('checked', checked);
  var total = $('.perm-group-' + grpId).length;
  updateGroupBadge(grpId, total);
}

// Cocher ou décocher globalement toutes les permissions
function checkAllGlobal(checked) {
  $('.perm-checkbox').prop('checked', checked);
  $('[id^="box-"]').each(function() {
    var grpId = $(this).attr('id').replace('box-', '');
    var total = $('.perm-group-' + grpId).length;
    updateGroupBadge(grpId, total);
  });
}

// Mise à jour en temps réel du badge "X / Y cochées"
function updateGroupBadge(grpId, total) {
  var checkedCount = $('.perm-group-' + grpId + ':checked').length;
  $('#badge-' + grpId).text(checkedCount + ' / ' + total + ' cochées');
  if (checkedCount === total) {
    $('#badge-' + grpId).css({'background': '#DCFCE7', 'color': '#15803D', 'borderColor': '#86EFAC'});
  } else if (checkedCount > 0) {
    $('#badge-' + grpId).css({'background': '#EFF6FF', 'color': '#1E3A5F', 'borderColor': '#BFDBFE'});
  } else {
    $('#badge-' + grpId).css({'background': '#F1F5F9', 'color': '#64748B', 'borderColor': '#CBD5E1'});
  }
}
</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
