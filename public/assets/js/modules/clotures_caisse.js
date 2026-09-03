/**
 * Module Clôtures de Caisse - Administration Olive Service
 * Gestion du registre journalier des clôtures et validation de caisse par la comptabilité
 */

$(function() {
  const racine = window.AppConfig ? window.AppConfig.racine : (window.RACINE || '/');
  const csrfToken = $('#csrf_token').val() || '';
  const isCommercial = window.AppConfig ? window.AppConfig.isCommercial : false;

  const $tableClo = $('#table-clotures_caisse');
  if ($tableClo.length) {
    const table = $tableClo.DataTable({
      ajax: racine + 'cloture_caisse/apiList',
      processing: true,
      autoWidth: false,
      columns: [
        { data: 'id_cloture', defaultContent: '-' },
        { 
          data: 'code_cloture', 
          render: (d) => `<code style="font-weight:700; color:#475569;">${d || '-'}</code>` 
        },
        { data: 'date_cloture', defaultContent: '-' },
        { data: 'nom_auteur_complet', defaultContent: '-' },
        { 
          data: 'total_especes', 
          render: (d) => d ? Number(d).toLocaleString('fr-FR') + ' F' : '0 F' 
        },
        { 
          data: 'total_mobile_money', 
          render: (d) => d ? Number(d).toLocaleString('fr-FR') + ' F' : '0 F' 
        },
        { 
          data: 'total_cheque_virement', 
          render: (d) => d ? Number(d).toLocaleString('fr-FR') + ' F' : '0 F' 
        },
        { 
          data: 'total_general', 
          render: (d) => `<strong style="color:#0F172A;">${d ? Number(d).toLocaleString('fr-FR') + ' FCFA' : '0 FCFA'}</strong>` 
        },
        { 
          data: 'statut_cloture', 
          width: '130px', 
          className: 'text-center', 
          render: function(d, type, row) {
            const val = d || 'attente';
            const bgColors = { 'valide': '#DCFCE7', 'attente': '#FEF3C7', 'rejete': '#FEE2E2' };
            const textColors = { 'valide': '#15803D', 'attente': '#B45309', 'rejete': '#B91C1C' };
            const borderColors = { 'valide': '#86EFAC', 'attente': '#FCD34D', 'rejete': '#FCA5A5' };
            const currentBg = bgColors[val] || '#F1F5F9';
            const currentText = textColors[val] || '#334155';
            const currentBorder = borderColors[val] || '#CBD5E1';

            if (isCommercial) {
              const labels = { 'valide': 'Validée', 'attente': 'En attente', 'rejete': 'Rejetée' };
              return `<span class="badge" style="background:${currentBg}; color:${currentText}; border:1px solid ${currentBorder}; padding:6px 10px; font-weight:700;">${labels[val] || val}</span>`;
            }

            return `
              <select class="select-statut-cloture" data-id="${row.id_cloture}" style="background:${currentBg}; color:${currentText}; border:1px solid ${currentBorder}; font-weight:700; font-size:12px; border-radius:8px; padding:4px 8px; cursor:pointer; outline:none;">
                <option value="attente" ${val === 'attente' ? 'selected' : ''} style="background:#fff; color:#B45309;">En attente</option>
                <option value="valide" ${val === 'valide' ? 'selected' : ''} style="background:#fff; color:#15803D;">Validée</option>
                <option value="rejete" ${val === 'rejete' ? 'selected' : ''} style="background:#fff; color:#B91C1C;">Rejetée</option>
              </select>`;
          } 
        },
        { 
          data: null, 
          orderable: false, 
          className: 'text-end',
          render: function(d) {
            const editId = d.editId || d.id_cloture;
            return `
              <a href="${racine}cloture_caisse/edition/${editId}" class="btn btn-sm btn-secondary me-1">
                <i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer
              </a>
              <a href="${racine}cloture_caisse/details/${editId}" class="btn btn-sm btn-info">
                <i data-lucide="eye" style="width:14px;height:14px;"></i> Détails
              </a>`;
          } 
        }
      ],
      language: { url: racine + 'json/datatables-i18n-fr-FR.json' },
      drawCallback: () => {
        if (window.lucide) lucide.createIcons();
      }
    });

    $(document).on('change', '.select-statut-cloture', function() {
      const id = $(this).data('id');
      const newStatut = $(this).val();

      $.ajax({
        url: racine + 'cloture_caisse/changer',
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
