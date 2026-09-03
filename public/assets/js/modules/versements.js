/**
 * Module Versements Commerciaux - Administration Olive Service
 * Suivi & Validation Caisse des versements commerciaux
 */

$(function() {
  const racine = window.AppConfig ? window.AppConfig.racine : (window.RACINE || '/');

  const $tableVers = $('#table-versements');
  if ($tableVers.length) {
    $tableVers.DataTable({
      ajax: racine + 'versement/apiList',
      processing: true,
      autoWidth: false,
      columns: [
        { 
          data: 'code_versement_commercial', 
          width: '130px', 
          render: (d) => d ? `<code class="vers-code-badge">${d}</code>` : '-' 
        },
        { 
          data: 'nom_commercial_complet', 
          render: (d) => `<strong style="color:#0F172A;">${d || '-'}</strong>` 
        },
        { data: 'libelle_zone', defaultContent: '-' },
        { 
          data: null, 
          defaultContent: '-', 
          render: function(d) {
            const debut = d.periode_versement_debut || '-';
            const fin = d.periode_versement_fin || '-';
            return `<small style="color:#64748B;">${debut} &rarr; ${fin}</small>`;
          }
        },
        { data: 'reference_versement', defaultContent: '-' },
        { 
          data: 'montant_versement', 
          render: (d) => `<strong style="color:#15803D; font-size:14px;">+${Number(d || 0).toLocaleString('fr-FR')} FCFA</strong>` 
        },
        { 
          data: 'statut_versement', 
          className: 'text-center', 
          width: '110px', 
          render: function(d) {
            let badge = 'bg-warning text-dark';
            if (d === 'valide') badge = 'bg-success';
            else if (d === 'ennule' || d === 'annule') badge = 'bg-danger';
            return `<span class="badge ${badge}">${d || 'En attente'}</span>`;
          }
        },
        { 
          data: null, 
          width: '180px', 
          orderable: false, 
          className: 'text-end',
          render: function(d) {
            const editId = d.editId || d.id_versement;
            return `
              <a href="${racine}versement/edition/${editId}" class="btn btn-sm btn-secondary me-1">
                <i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer
              </a>
              <a href="${racine}versement/details/${editId}" class="btn btn-sm btn-info">
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
  }
});
