<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      
      <!-- EN-TÊTE DE PAGE -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="clock" style="color: #1E3A5F; width: 26px; height: 26px;"></i>
            <span>Sessions d'Activité & Cotisations</span>
          </h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Gestion des sessions et campagnes annuelles de collecte Olive Service</p>
        </div>
        <a href="<?= RACINE ?>session/formulaire" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
          <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Nouvelle Session
        </a>
      </div>

      <!-- CARTE TABLEAU PRINCIPALE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; max-width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
          <table id="table-sessions" class="table display nowrap" style="width:100%; max-width:100%; border-collapse: collapse;">
            <thead>
              <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                <th style="padding: 12px;">Réf Code</th>
                <th style="padding: 12px;">Libellé Session</th>
                <th style="padding: 12px;">Année </th>
                <th style="padding: 12px;">Zone </th>
                <th style="padding: 12px; text-align: center;">Durée Jours</th>
                <th style="padding: 12px;">Période (Début - Fin)</th>
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

<script>
$(document).ready(function() {
  var table = $('#table-sessions').DataTable({
    ajax: '<?= RACINE ?>session/apiList',
    processing: true,
    autoWidth: false,
    columns: [
      { data: 'code_session', width: '120px', render: function(d) {
        if (!d) return '-';
        return '<code style="font-weight:800; color:#1E3A5F; background:#EFF6FF; padding:3px 8px; border-radius:6px;">' + d + '</code>';
      }},
      { data: 'libelle_session', render: function(d) {
        return '<strong style="color:#0F172A; font-size:14px;">' + (d || '-') + '</strong>';
      }},
      { data: 'libelle_annee', defaultContent: '-' },
      { data: 'libelle_zone', defaultContent: 'Non spécifiée', render: function(d) {
        if (!d || d === 'Non spécifiée') return '<span style="color:#94A3B8; font-style:italic;">Non spécifiée</span>';
        return '<span style="display:inline-block; position:static; background:#EFF6FF; color:#1E3A5F; padding:5px 10px; border-radius:8px; font-weight:700; border:1px solid #BFDBFE;">' + d + '</span>';
      }},
      { data: 'nombre_jour_session', className: 'text-center', render: function(d, type, row) {
        var days = parseInt(d || 0, 10);
        if ((!days || days <= 0) && row.date_debut_session && row.date_fin_session) {
          var d1 = new Date(row.date_debut_session);
          var d2 = new Date(row.date_fin_session);
          var diff = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
          if (diff > 0) days = diff;
        }
        return '<span style="display:inline-block; position:static; background:#F8FAFC; color:#334155; padding:5px 10px; border-radius:8px; font-weight:800; border:1px solid #E2E8F0;">' + (days || 0) + ' jours</span>';
      }},
      { data: null, render: function(d, type, row) {
        var deb = row.date_debut_session ? row.date_debut_session : 'N/A';
        var fin = row.date_fin_session ? row.date_fin_session : 'N/A';
        return '<small style="color:#64748B;">' + deb + ' &rarr; ' + fin + '</small>';
      }},
      { data: 'statut_session', width: '90px', className: 'text-center', render: function(d, type, row) {
        var isActif = (d === 'actif');
        var checkedAttr = isActif ? 'checked' : '';
        return '<div style="display:flex; justify-content:center; align-items:center;">' +
               '<label style="position:relative; display:inline-block; width:38px; height:20px; margin:0; cursor:pointer;" title="' + (isActif ? 'Actif - Cliquez pour désactiver' : 'Inactif - Cliquez pour activer') + '">' +
               '<input type="checkbox" class="toggle-statut-session" data-id="' + row.id_session + '" ' + checkedAttr + ' style="opacity:0; width:0; height:0;">' +
               '<span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:' + (isActif ? '#15803D' : '#CBD5E1') + '; transition:.3s; border-radius:20px;">' +
               '<span style="position:absolute; content:\'\'; height:14px; width:14px; left:' + (isActif ? '20px' : '3px') + '; bottom:3px; background-color:white; transition:.3s; border-radius:50%;"></span>' +
               '</span>' +
               '</label>' +
               '</div>';
      }},
      { data: null, width: '160px', orderable: false, render: function(d) {
        return '<a href="' + window.RACINE + 'session/edition/' + (d.editId || d.id_session) + '" class="btn btn-sm btn-secondary" style="margin-right:6px; font-weight:600; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer</a>' +
               '<a href="' + window.RACINE + 'session/details/' + (d.editId || d.id_session) + '" class="btn btn-sm btn-info" style="font-weight:600; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i data-lucide="eye" style="width:14px;height:14px;"></i> Détails</a>';
      }, className: 'text-end' }
    ],
    language: { url: '<?= RACINE ?>json/datatables-i18n-fr-FR.json' },
    drawCallback: function() { if (window.lucide) lucide.createIcons(); }
  });

  $(document).on('change', '.toggle-statut-session', function() {
    var id = $(this).data('id');
    var isChecked = $(this).is(':checked');
    var $input = $(this);

    $.ajax({
      url: '<?= RACINE ?>session/changer',
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
