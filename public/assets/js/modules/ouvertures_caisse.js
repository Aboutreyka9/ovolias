/**
 * Module Ouvertures de Caisse - Administration Olive Service
 * Gestion de l'historique et des statuts des ouvertures de caisse
 */

$(function() {
  const racine = window.AppConfig ? window.AppConfig.racine : (window.RACINE || '/');
  const csrfToken = $('#csrf_token').val() || '';

  const $tableOuv = $('#table_ouvertures_caisse');
  if ($tableOuv.length) {
    const table = $tableOuv.DataTable({
      ajax: {
        url: racine + 'ouverture_caisse/apiList',
        type: 'GET'
      },
      columns: [
        { data: 'id_ouverture' },
        { 
          data: 'code_ouverture',
          render: (d) => d ? `<code style="font-weight:700; color:#1E3A5F;">${d}</code>` : '-'
        },
        { 
          data: 'date_ouverture',
          render: (d) => d ? new Date(d).toLocaleDateString('fr-FR') : '-'
        },
        { data: 'heure_ouverture', defaultContent: '-' },
        { 
          data: 'fond_initial',
          render: (d) => `<strong style="color:#0F172A;">${Number(d || 0).toLocaleString('fr-FR')} FCFA</strong>`
        },
        { data: 'nom_auteur_complet', defaultContent: '-' },
        { 
          data: 'statut_ouverture',
          width: '130px',
          className: 'text-center',
          render: function(d, type, row) {
            const val = d || 'ouverte';
            const isOuverte = (val === 'ouverte');
            const currentBg = isOuverte ? '#DCFCE7' : '#F1F5F9';
            const currentText = isOuverte ? '#166534' : '#475569';
            const currentBorder = isOuverte ? '#86EFAC' : '#CBD5E1';

            return `
              <select class="select-statut-ouverture" data-id="${row.id_ouverture}" style="background:${currentBg}; color:${currentText}; border:1px solid ${currentBorder}; font-weight:700; font-size:12px; border-radius:8px; padding:4px 8px; cursor:pointer; outline:none;">
                <option value="ouverte" ${isOuverte ? 'selected' : ''} style="background:#fff; color:#166534;">Ouverte</option>
                <option value="cloturee" ${!isOuverte ? 'selected' : ''} style="background:#fff; color:#475569;">Clôturée</option>
              </select>`;
          }
        },
        { 
          data: null,
          orderable: false,
          className: 'text-end',
          render: function(data, type, row) {
            const editId = row.editId || row.id_ouverture;
            return `
              <a href="${racine}ouverture_caisse/edition/${editId}" class="btn btn-sm btn-info" style="border-radius:6px; font-weight:600; padding:4px 10px;">
                <i data-lucide="edit-3" style="width:14px; height:14px;"></i> Éditer
              </a>`;
          }
        }
      ],
      language: { url: racine + 'json/datatables-i18n-fr-FR.json' },
      drawCallback: () => {
        if (window.lucide) lucide.createIcons();
      }
    });

    $(document).on('change', '.select-statut-ouverture', function() {
      const id = $(this).data('id');
      const newStatut = $(this).val();

      $.ajax({
        url: racine + 'ouverture_caisse/changer',
        type: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        data: {
          id: id,
          statut: newStatut,
          csrf_token: csrfToken
        },
        dataType: 'json',
        success: function(res) {
          if (res.status === 1 || res.success) {
            if (window.toastr) toastr.success(res.message || 'Statut mis à jour avec succès');
            table.ajax.reload(null, false);
          } else {
            if (window.toastr) toastr.error(res.message || 'Erreur lors du changement de statut');
            table.ajax.reload(null, false);
          }
        },
        error: function() {
          if (window.toastr) toastr.error('Erreur réseau');
          table.ajax.reload(null, false);
        }
      });
    });
  }
});
