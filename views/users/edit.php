<?php
require_once __DIR__ . '/../../public/inc/header.php';
$user = isset($user) ? $user : [];
$role = isset($role) ? $role : [];
$roles = isset($roles) ? $roles : (new ModelRole())->getAll();
$fonctions = isset($fonctions) ? $fonctions : (new ModelFonction())->getAll();
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px;">
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;"><?= isset($user['id_user']) ? 'Modifier l\'Utilisateur' : 'Créer un Compte Utilisateur' ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Gestion des identités et des attributions de rôles OVOLIA</p>
        </div>
        <a href="<?= RACINE ?>user/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
          <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour à la liste
        </a>
      </div>

      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <form action="<?= RACINE ?>user/<?= !empty($user['id_user']) ? 'edit' : 'add' ?>" method="POST" style="width: 100%;">
          <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
          <?php if (!empty($user['id_user'])): ?>
            <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">
          <?php endif; ?>

          <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 8px;">
            <i data-lucide="user" style="width: 18px; height: 18px;"></i> Identité & Coordonnées
          </h3>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; width: 100%; margin-bottom: 24px;">
            <div class="form-group" style="width: 100%; box-sizing: border-box;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Nom de famille <span style="color: #EF4444;">*</span></label>
              <input type="text" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF; color: #0F172A;" name="nom" value="<?= htmlspecialchars($user['nom_user'] ?? '') ?>" placeholder="Ex: KOUASSI" required>
            </div>

            <div class="form-group" style="width: 100%; box-sizing: border-box;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Prénom(s)</label>
              <input type="text" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF; color: #0F172A;" name="prenom" value="<?= htmlspecialchars($user['prenom_user'] ?? '') ?>" placeholder="Ex: Jean-Marc">
            </div>

            <div class="form-group" style="width: 100%; box-sizing: border-box;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Adresse Email (Identifiant de connexion)</label>
              <input type="email" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF; color: #0F172A;" name="email" value="<?= htmlspecialchars($user['email_user'] ?? '') ?>" placeholder="Ex: utilisateur@geicg.ci" required>
            </div>

            <div class="form-group" style="width: 100%; box-sizing: border-box;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Numéro Téléphone</label>
              <input type="text" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF; color: #0F172A;" name="telephone" value="<?= htmlspecialchars($user['telephone_user'] ?? '') ?>" placeholder="Ex: 0708091011">
            </div>

            <div class="form-group" style="width: 100%; box-sizing: border-box;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Fonction / Poste</label>
              <select class="form-control select2" id="sel_fonction_user" name="fonction_code" style="width: 100%;">
                <option value="">-- Sélectionner un poste --</option>
                <?php foreach($fonctions as $f): ?>
                  <option value="<?= htmlspecialchars($f['code_fonction']) ?>" <?= (($user['fonction_code'] ?? '') == $f['code_fonction']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($f['libelle_fonction']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group" style="width: 100%; box-sizing: border-box;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Zone d'Affectation / Secteur <span style="color: #64748B; font-weight: 500; font-size: 12px;">(Optionnel)</span></label>
              <select class="form-control select2" id="sel_zone_user" name="zone_user" style="width: 100%;">
                <option value="">-- Aucune zone (Super Admin / Global) --</option>
                <?php if (!empty($zones)): foreach($zones as $z): ?>
                  <option value="<?= htmlspecialchars($z['code_zone']) ?>" <?= (($user['zone_user'] ?? '') == $z['code_zone']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($z['libelle_zone']) ?>
                  </option>
                <?php endforeach; endif; ?>
              </select>
              <small style="color: #64748B; font-size: 11px; margin-top: 4px; display: block;">Laissez vide si l'utilisateur est un Super Admin ou n'est pas restreint à une zone.</small>
            </div>

            <?php if (empty($user['id_user'])): ?>
            <div class="form-group" style="width: 100%; box-sizing: border-box; grid-column: 1 / -1; background: #F0FDF4; border: 1.5px dashed #86EFAC; border-radius: 8px; padding: 12px 16px;">
              <div style="font-size: 13px; font-weight: 700; color: #166534; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="shield-check" style="width: 18px; height: 18px; color: #16A34A;"></i>
                Sécurité : Mot de passe généré automatiquement
              </div>
              <small style="color: #15803D; font-size: 12px; margin-top: 4px; display: block;">
                Un mot de passe sécurisé sera généré automatiquement par le serveur en arrière-plan et affiché dès l'enregistrement du compte.
              </small>
            </div>
            <?php endif; ?>
          </div>

          <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 24px 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 8px;">
            <i data-lucide="shield" style="width: 18px; height: 18px;"></i> Attribution des Rôles & Droits d'Accès
          </h3>

          <?php 
            $selectedRoleCodes = isset($userRoleCodes) ? $userRoleCodes : (isset($role['role_code']) ? [$role['role_code']] : []);
          ?>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; width: 100%; margin-bottom: 24px;">
            <div class="form-group" style="width: 100%; box-sizing: border-box; grid-column: 1 / -1;">
              <label style="display: flex; justify-content: space-between; align-items: center; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">
                <span>Rôle(s) attribué(s) à l'utilisateur <span style="color: #EF4444;">*</span></span>
                <small style="color: #64748B; font-weight: normal; font-size: 11.5px;">Sélection multiple possible (cumul des accès)</small>
              </label>
              <select class="form-control select2" id="sel_roles_user" name="roles[]" multiple="multiple" style="width: 100%;" required>
                <?php foreach($roles as $r): ?>
                  <option value="<?= htmlspecialchars($r['code_role']) ?>" <?= in_array($r['code_role'], $selectedRoleCodes, true) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['libelle_role'] . ' (' . ($r['groupe'] ?? $r['module']) . ')') ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <small style="color: #64748B; font-size: 11.5px; margin-top: 6px; display: block;">
                💡 <span style="font-weight: 600;">Exemple :</span> Un utilisateur peut être simultanément <em>Commercial</em> et <em>Caissier</em>, ou <em>Responsable Finance</em> et <em>Chef Comptable</em>.
              </small>
            </div>
          </div>

          <div style="display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0; width: 100%;">
            <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 10px 24px;">Enregistrer l'Utilisateur</button>
            <a href="<?= RACINE ?>user/list" class="btn btn-secondary" style="font-weight: 600; border-radius: 8px; padding: 10px 24px;">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
<script>
$(document).ready(function() { 
  if (window.lucide) lucide.createIcons();
  if ($.fn.select2) {
    $('#sel_fonction_user').select2({ placeholder: "-- Sélectionner un poste --", allowClear: true, width: '100%' });
    $('#sel_zone_user').select2({ placeholder: "-- Aucune zone (Super Admin / Global) --", allowClear: true, width: '100%' });
    $('#sel_roles_user').select2({ placeholder: "Sélectionnez un ou plusieurs rôles", closeOnSelect: false, width: '100%' });
  }
});
</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
