<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php
  $userRoles = $_SESSION[USERS_AUTH]['roles'] ?? [];
  if (empty($userRoles)) {
      $singleRole = $_SESSION[USERS_AUTH]['role_code'] ?? ($_SESSION['role_code'] ?? '');
      $userRoles = !empty($singleRole) ? [$singleRole] : [];
  }
  $isSuperAdminUser = in_array('ROLE_SUPERADMIN', $userRoles, true);
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">Utilisateurs Système & Sécurité</h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Gestion des comptes du personnel, rôles attribués et accès sécurisés</p>
        </div>
        <a href="<?= RACINE ?>user/formulaire" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
          <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i> Nouvel Utilisateur
        </a>
      </div>
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; max-width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
          <table id="table-users" class="table display nowrap" style="width:100%; max-width:100%; border-collapse: collapse;">
            <thead>
              <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Code</th>
                <th style="padding: 12px;">Nom complet</th>
                <th style="padding: 12px;">Contact (Email / Tél)</th>
                <th style="padding: 12px;">Fonction</th>
                <th style="padding: 12px;">Zone</th>
                <th style="padding: 12px;">Rôle Attribué</th>
                <th style="padding: 12px;" class="text-center">Statut</th>
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
<script>
var IS_SUPER_ADMIN_USER = <?= $isSuperAdminUser ? 'true' : 'false' ?>;

$(document).ready(function() {
  var table = $('#table-users').DataTable({
    ajax: '<?= RACINE ?>user/apiList',
    processing: true,
    autoWidth: false,
    columns: [
      { data: 'id', defaultContent: '-', width: '50px' },
      { data: 'code', width: '100px', render: function(d, type) {
        if (type !== 'display') return d || '';
        return '<code style="font-weight:700; color:#475569;">' + (d || '-') + '</code>';
      }},
      { data: 'nom', render: function(d, type, row) {
        if (type !== 'display') return (d || '') + ' ' + (row.prenom || '');
        var nomComplet = (d || '') + ' ' + (row.prenom || '');
        return '<strong style="color:#0F172A;">' + (nomComplet.trim() || '-') + '</strong>';
      }},
      { data: 'email', render: function(d, type, row) {
        if (type !== 'display') return d || row.telephone || '';
        var res = '';
        if (d) res += '<div style="font-weight:600; color:#1E3A5F; font-size:13px;">' + d + '</div>';
        if (row.telephone) res += '<div style="font-size:12px; color:#64748B;">' + row.telephone + '</div>';
        return res || '-';
      }},
      { data: 'fonction', defaultContent: '-', render: function(d, type) {
        if (type !== 'display') return d || '';
        return '<span style="color:#334155; font-weight:500;">' + (d || '-') + '</span>';
      }},
      { data: 'zone', defaultContent: 'Globale', render: function(d, type, row) {
        var zoneVal = d || (row && row.zone ? row.zone : 'Globale');
        if (type !== 'display') return zoneVal;
        if (!zoneVal || zoneVal === 'Globale') {
          return '<span style="display:inline-block; position:static; background:#F1F5F9; color:#64748B; padding:4px 8px; border-radius:6px; font-weight:600; border:1px solid #E2E8F0;">Globale</span>';
        }
        return '<span style="display:inline-block; position:static; background:#EFF6FF; color:#1E3A5F; padding:4px 8px; border-radius:6px; font-weight:700; border:1px solid #BFDBFE;">' + zoneVal + '</span>';
      }},
      { data: 'roles_list', render: function(d, type, row) {
        if (type !== 'display') return (row.roles_list && row.roles_list.length) ? row.roles_list.join(', ') : (row.role || '');
        var roles = (row.roles_list && row.roles_list.length) ? row.roles_list : (row.role ? [row.role] : []);
        if (!roles.length || roles[0] === 'Non attribué') {
          return '<span style="color:#94A3B8; font-style:italic; font-size:12px;">Non attribué</span>';
        }
        var badges = roles.map(function(r) {
          return '<span style="background:rgba(24, 56, 95, 0.08); color:var(--primary-color, #18385F); border: 1px solid rgba(24, 56, 95, 0.18); font-weight:700; padding:3px 8px; border-radius:6px; font-size:11.5px; display:inline-block; margin:2px 2px;">' + r + '</span>';
        });
        return '<div style="display:flex; flex-wrap:wrap; gap:3px; max-width:260px;">' + badges.join('') + '</div>';
      }},
      { data: 'statut', width: '120px', className: 'text-center', render: function(d, type, row) {
        var isActif = (d === 'actif');
        var checkedAttr = isActif ? 'checked' : '';
        var isPending = !!row.token_pending;
        
        var tooltipMsg = isPending
          ? (IS_SUPER_ADMIN_USER ? 'Compte non activé par jeton (Dérogation Super Admin active)' : 'Jeton d\'activation non validé - Modification réservée au Super Admin')
          : (isActif ? 'Actif - Cliquez pour désactiver' : 'Inactif - Cliquez pour activer');

        var containerStyle = (isPending && !IS_SUPER_ADMIN_USER) ? 'opacity: 0.6; cursor: not-allowed;' : 'cursor: pointer;';

        var html = '<div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px;">';
        html += '<label style="position:relative; display:inline-block; width:38px; height:20px; margin:0; ' + containerStyle + '" title="' + tooltipMsg + '">';
        html += '<input type="checkbox" class="toggle-statut-user" data-id="' + (row.id || row.id_user) + '" data-pending="' + (isPending ? '1' : '0') + '" ' + checkedAttr + ' style="opacity:0; width:0; height:0;">';
        html += '<span style="position:absolute; top:0; left:0; right:0; bottom:0; background-color:' + (isActif ? '#15803D' : '#CBD5E1') + '; transition:.3s; border-radius:20px;">';
        html += '<span style="position:absolute; content:\'\'; height:14px; width:14px; left:' + (isActif ? '20px' : '3px') + '; bottom:3px; background-color:white; transition:.3s; border-radius:50%;"></span>';
        html += '</span>';
        html += '</label>';

        if (isPending) {
          html += '<span style="background:#FEF3C7; color:#B45309; border:1px solid #FDE68A; font-size:10.5px; padding:3px 7px; border-radius:6px; font-weight:700; white-space:nowrap; display:inline-block;" title="En attente de validation du lien mail">Jeton non activé</span>';
        }

        html += '</div>';
        return html;
      }},
      { data: null, width: '160px', orderable: false, render: function(d) {
        return '<a href="' + window.RACINE + 'user/edition/' + (d.editId || d.id) + '" class="btn btn-sm btn-secondary" style="margin-right:6px; font-weight:600; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer</a>' +
               '<a href="' + window.RACINE + 'user/details/' + (d.editId || d.id) + '" class="btn btn-sm btn-info" style="font-weight:600; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i data-lucide="eye" style="width:14px;height:14px;"></i> Profil</a>';
      }, className: 'text-end' }
    ],
    language: { url: '<?= RACINE ?>json/datatables-i18n-fr-FR.json' },
    drawCallback: function() { if (window.lucide) lucide.createIcons(); }
  });

  // Bascule de statut instantanée via Ajax
  $(document).on('change', '.toggle-statut-user', function(e) {
    var id = $(this).data('id');
    var isPending = $(this).data('pending') == '1';
    var isChecked = $(this).is(':checked');
    var $input = $(this);

    if (isPending && !IS_SUPER_ADMIN_USER) {
      if (window.toastr) {
        toastr.warning("Action bloquée : Ce compte est en attente d'activation par l'utilisateur via le jeton reçu par email. Seul un Super Admin est autorisé à modifier son statut.");
      } else {
        alert("Action bloquée : Seul un Super Admin peut modifier le statut d'un compte avec jeton non activé.");
      }
      $input.prop('checked', !isChecked);
      return false;
    }

    $.ajax({
      url: '<?= RACINE ?>user/changer',
      type: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      data: {
        id: id,
        csrf_token: '<?= Validator::generateCsrfToken() ?>'
      },
      dataType: 'json',
      success: function(res) {
        if (res.status === 1 || res.success) {
          if (window.toastr) toastr.success(res.message || 'Statut mis à jour avec succès');
          table.ajax.reload(null, false);
        } else {
          if (window.toastr) toastr.error(res.message || 'Erreur lors du changement de statut');
          $input.prop('checked', !isChecked);
        }
      },
      error: function() {
        if (window.toastr) toastr.error('Erreur réseau');
        $input.prop('checked', !isChecked);
      }
    });
  });
});
</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
