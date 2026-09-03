/**
 * Module Client - Administration Olive Service
 * Gestion DataTables & Interactivité Clients
 */

$(function() {
  const racine = window.AppConfig ? window.AppConfig.racine : (window.RACINE || '/');

  const $tableClients = $('#table-clients');
  if ($tableClients.length) {
    $tableClients.DataTable({
      ajax: racine + 'client/apiList',
      processing: true,
      autoWidth: false,
      columns: [
        { data: 'id_client', defaultContent: '-', width: '50px' },
        { 
          data: 'code_client', 
          width: '120px', 
          render: (d) => d ? `<code class="client-code-badge">${d}</code>` : '-' 
        },
        { 
          data: 'nom_complet', 
          render: (d) => `<strong style="color:#0F172A;">${d || '-'}</strong>` 
        },
        { data: 'telephone_client', defaultContent: '-' },
        { data: 'cni_client', defaultContent: '-' },
        { data: 'lieu_residence_client', defaultContent: '-' },
        { 
          data: null, 
          width: '160px', 
          orderable: false, 
          className: 'text-end',
          render: function(d) {
            const editId = d.editId || d.id_client;
            return `
              <a href="${racine}client/edition/${editId}" class="btn btn-sm btn-secondary me-1">
                <i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer
              </a>
              <a href="${racine}client/details/${editId}" class="btn btn-sm btn-info">
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
