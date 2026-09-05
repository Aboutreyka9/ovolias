/**
 * Module Cotisations Terrain - Administration Olive Service
 * Gestion DataTables des cotisations quotidiennes
 */

$(function() {
  const racine = window.AppConfig ? window.AppConfig.racine : (window.RACINE || '/');

  const $tableCotis = $('#table-cotisations');
  if ($tableCotis.length) {
    $tableCotis.DataTable({
      ajax: racine + 'cotisation/apiList',
      processing: true,
      autoWidth: false,
      columns: [
        { 
          data: 'code_cautisation_client', 
          width: '130px', 
          render: (d) => d ? `<code class="cotis-code-badge">${d}</code>` : '-' 
        },
        { data: 'date_cautisation', defaultContent: '-', width: '100px' },
        { 
          data: 'nom_client_complet', 
          render: (d) => `<strong style="color:#0F172A;">${d || '-'}</strong>` 
        },
        { data: 'libelle_pack', defaultContent: '-' },
        { data: 'nom_commercial_complet', defaultContent: '-' },
        { 
          data: 'mode_paiement', 
          defaultContent: '-', 
          width: '90px', 
          className: 'text-center', 
          render: function(d) {
            let badge = 'bg-secondary';
            if (d === 'espece') badge = 'bg-success';
            else if (d === 'mobile_money') badge = 'bg-info';
            else if (d === 'virement') badge = 'bg-primary';
            return `<span class="badge ${badge}">${d || '-'}</span>`;
          }
        },
        { 
          data: 'nombre_jour', 
          className: 'text-center', 
          width: '70px', 
          render: (d) => `<span class="badge bg-secondary">+${d || 1} j</span>` 
        },
        { 
          data: 'montant_cautisation_client', 
          render: (d) => `<strong style="color:#15803D; font-size:14px;">${Number(d || 0).toLocaleString('fr-FR')} FCFA</strong>` 
        },
        { 
          data: null, 
          width: '160px', 
          orderable: false, 
          className: 'text-end',
          render: function(d) {
            const editId = d.editId || d.id_cautisation_client;
            return `
              <a href="${racine}cotisation/edition/${editId}" class="btn btn-sm btn-secondary me-1">
                <i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer
              </a>
              <a href="${racine}cotisation/details/${editId}" class="btn btn-sm btn-info">
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
