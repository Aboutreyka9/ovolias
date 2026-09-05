/**
 * Module Packs - Administration Olive Service
 * Offres produits, regroupement par Année/Zone et activation Ajax
 */

$(function() {
  const racine = window.AppConfig ? window.AppConfig.racine : (window.RACINE || '/');
  const csrfToken = $('#csrf_token').val() || '';

  const $tablePacks = $('#table-packs');
  if ($tablePacks.length) {
    const table = $tablePacks.DataTable({
      ajax: racine + 'pack/apiList',
      processing: true,
      autoWidth: false,
      order: [],
      columns: [
        { data: 'id_pack', defaultContent: '-', width: '50px' },
        { 
          data: 'code_pack', 
          width: '120px', 
          render: (d) => d ? `<code class="pack-code-badge">${d}</code>` : '-' 
        },
        { 
          data: 'libelle_pack', 
          render: (d) => `<strong style="color:#0F172A;">${d || '-'}</strong>` 
        },
        { data: 'libelle_categorie', defaultContent: '-' },
        { data: 'libelle_session', defaultContent: '-' },
        { data: 'libelle_zone', defaultContent: '-' },
        { 
          data: 'nombre_jour_session', 
          width: '100px', 
          className: 'text-center', 
          render: (d) => `<span style="font-weight:700; color:#334155;">${Number(d || 0)}</span>` 
        },
        { 
          data: 'prix_cotisation_pack', 
          width: '140px', 
          className: 'text-end', 
          render: (d) => `<span style="font-weight:700; color:#15803D;">${Number(d || 0).toLocaleString('fr-FR')} FCFA</span>` 
        },
        { 
          data: 'montant_total', 
          width: '140px', 
          className: 'text-end', 
          render: (d) => `<strong style="color:#1E3A5F;">${Number(d || 0).toLocaleString('fr-FR')} FCFA</strong>` 
        },
        { 
          data: 'statut_pack', 
          width: '80px', 
          className: 'text-center', 
          render: function(d, type, row) {
            const isActif = (d === 'actif');
            const checkedAttr = isActif ? 'checked' : '';
            return `
              <div style="display:flex; justify-content:center; align-items:center;">
                <label style="position:relative; display:inline-block; width:38px; height:20px; margin:0; cursor:pointer;" title="${isActif ? 'Actif - Cliquez pour désactiver' : 'Inactif - Cliquez pour activer'}">
                  <input type="checkbox" class="toggle-statut-pack" data-id="${row.id_pack}" ${checkedAttr} style="opacity:0; width:0; height:0;">
                  <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:${isActif ? '#15803D' : '#CBD5E1'}; transition:.3s; border-radius:20px;">
                    <span style="position:absolute; content:''; height:14px; width:14px; left:${isActif ? '20px' : '3px'}; bottom:3px; background-color:white; transition:.3s; border-radius:50%;"></span>
                  </span>
                </label>
              </div>`;
          }
        },
        { 
          data: null, 
          width: '180px', 
          orderable: false, 
          className: 'text-end',
          render: function(d) {
            const editId = d.editId || d.id_pack;
            return `
              <a href="${racine}pack/edition/${editId}" class="btn btn-sm btn-secondary me-1">
                <i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer
              </a>
              <a href="${racine}pack/details/${editId}" class="btn btn-sm btn-info">
                <i data-lucide="eye" style="width:14px;height:14px;"></i> Détails
              </a>
            `;
          } 
        }
      ],
      language: { url: racine + 'json/datatables-i18n-fr-FR.json' },
      drawCallback: function() {
        const api = this.api();
        const rows = api.rows({ page: 'current' }).nodes();
        let lastGroup = null;

        api.rows({ page: 'current' }).data().each(function(rowData, i) {
          const anneeName = rowData.libelle_annee || rowData.annee_code || '-';
          const zoneName = rowData.libelle_zone || rowData.zone_code || '-';
          const groupKey = `Année : ${anneeName} | Zone : ${zoneName}`;

          if (lastGroup !== groupKey) {
            $(rows).eq(i).before(`
              <tr class="group-header" style="background:#F1F5F9; font-weight:700; color:#1E3A5F;">
                <td colspan="11" style="padding:10px 14px; border-top:2px solid #CBD5E1; border-bottom:1px solid #CBD5E1;">
                  <div style="display:flex; align-items:center; gap:8px;">
                    <i data-lucide="layers" style="width:16px; height:16px; color:#1E3A5F;"></i>
                    <span>${groupKey}</span>
                  </div>
                </td>
              </tr>
            `);
            lastGroup = groupKey;
          }
        });

        if (window.lucide) lucide.createIcons();
      }
    });

    // Toggle statut pack via Ajax
    $(document).on('change', '.toggle-statut-pack', function() {
      const id = $(this).data('id');
      const isChecked = $(this).is(':checked');
      const $input = $(this);

      $.ajax({
        url: racine + 'pack/changer',
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
