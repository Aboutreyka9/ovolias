/**
 * Module Dépenses d'Exploitation - Administration Olive Service
 * Gestion DataTables, affichage des statuts et activation/désactivation Ajax
 */

$(function() {
  const racine = window.AppConfig ? window.AppConfig.racine : (window.RACINE || '/');
  const csrfToken = $('#csrf_token').val() || '';

  const $tableDep = $('#table-depenses');
  if ($tableDep.length) {
    const table = $tableDep.DataTable({
      ajax: racine + 'depense/apiList',
      processing: true,
      autoWidth: false,
      columns: [
        { 
          data: 'code_depense', 
          width: '120px', 
          render: (d) => d ? `<code class="dep-code-badge">${d}</code>` : '-' 
        },
        { data: 'date_depense', defaultContent: '-' },
        { 
          data: 'libelle_type_depense', 
          render: (d) => `<span class="badge bg-light text-dark" style="border:1px solid #CBD5E1; font-weight:700;">${d || '-'}</span>` 
        },
        { data: 'motif_depense', defaultContent: '-' },
        { 
          data: 'montant_depense', 
          render: (d) => `<strong style="color:#DC2626; font-size:14px;">-${Number(d || 0).toLocaleString('fr-FR')} FCFA</strong>` 
        },
        { data: 'nom_auteur_complet', defaultContent: '-' },
        { 
          data: 'statut_depense', 
          width: '120px', 
          className: 'text-center', 
          render: function(d, type, row) {
            const isActif = (d === 'actif');
            const checkedAttr = isActif ? 'checked' : '';
            return `
              <div style="display:flex; justify-content:center; align-items:center; gap:8px;">
                <label style="position:relative; display:inline-block; width:38px; height:20px; margin:0; cursor:pointer;" title="${isActif ? 'Actif - Cliquez pour désactiver' : 'Inactif - Cliquez pour activer'}">
                  <input type="checkbox" class="toggle-statut-depense" data-id="${row.id_depense}" ${checkedAttr} style="opacity:0; width:0; height:0;">
                  <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:${isActif ? '#15803D' : '#CBD5E1'}; transition:.3s; border-radius:20px;">
                    <span style="position:absolute; content:''; height:14px; width:14px; left:${isActif ? '20px' : '3px'}; bottom:3px; background-color:white; transition:.3s; border-radius:50%;"></span>
                  </span>
                </label>
                <span class="badge ${isActif ? 'bg-success' : 'bg-warning text-dark'}">${isActif ? 'Actif' : 'Inactif'}</span>
              </div>`;
          }
        },
        { 
          data: null, 
          width: '160px', 
          orderable: false, 
          className: 'text-end',
          render: function(d) {
            const editId = d.editId || d.id_depense;
            return `
              <a href="${racine}depense/edition/${editId}" class="btn btn-sm btn-secondary me-1">
                <i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer
              </a>
              <a href="${racine}depense/details/${editId}" class="btn btn-sm btn-info">
                <i data-lucide="eye" style="width:14px;height:14px;"></i> Détails
              </a>
            `;
          } 
        }
      ],
      language: { url: racine + 'json/datatables-i18n-fr-FR.json' },
      drawCallback: () => {
        if (window.lucide) lucide.createIcons();
      }
    });

    // Toggle statut dépense via Ajax
    $(document).on('change', '.toggle-statut-depense', function() {
      const id = $(this).data('id');
      const isChecked = $(this).is(':checked');
      const $input = $(this);

      $.ajax({
        url: racine + 'depense/changer',
        type: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        data: { id: id, csrf_token: csrfToken },
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
  }
});
