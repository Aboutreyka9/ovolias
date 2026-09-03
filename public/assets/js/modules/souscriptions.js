/**
 * Module Souscription - Administration Olive Service
 * DataTables, Calculs de progression & Gestion des Contrats
 */

$(function() {
  const racine = window.AppConfig ? window.AppConfig.racine : (window.RACINE || '/');

  const $tableSouscr = $('#table-souscriptions');
  if ($tableSouscr.length) {
    $tableSouscr.DataTable({
      ajax: racine + 'souscription/apiList',
      processing: true,
      autoWidth: false,
      columns: [
        { 
          data: 'code_souscription', 
          width: '130px', 
          render: (d) => d ? `<code class="souscription-code-badge">${d}</code>` : '-' 
        },
        { 
          data: 'statut_souscription', 
          className: 'text-center', 
          width: '100px', 
          render: function(d) {
            let badge = 'bg-primary';
            let libelle = 'Validée';
            if (d === 'solde') { badge = 'bg-success'; libelle = 'Soldée'; }
            else if (d === 'annule') { badge = 'bg-danger'; libelle = 'Annulée'; }
            else if (d === 'reconduite') { badge = 'bg-warning text-dark'; libelle = 'Reconduite'; }
            return `<span class="badge ${badge}">${libelle}</span>`;
          }
        },
        { 
          data: 'nom_client_complet', 
          render: (d) => `<strong style="color:#0F172A;">${d || '-'}</strong>` 
        },
        { data: 'libelle_session', defaultContent: '-' },
        { 
          data: 'sum_prix_cotisation_pack', 
          render: (d) => `<span style="font-weight:700; color:#15803D;">${Number(d || 0).toLocaleString('fr-FR')} FCFA</span>` 
        },
        { 
          data: 'totale_souscription', 
          className: 'text-end',
          render: (d) => `<strong style="color:#1E3A5F;">${Number(d || 0).toLocaleString('fr-FR')} FCFA</strong>` 
        },
        { 
          data: null, 
          render: function(d) {
            const cotise = d.nombre_jour_cotise || 0;
            const total = d.nombre_jour_session || 0;
            const pct = total > 0 ? Math.round((cotise / total) * 100) : 0;
            const color = pct >= 100 ? '#15803D' : (pct >= 50 ? '#D97706' : '#DC2626');
            return `
              <div style="min-width:120px;">
                <strong>${cotise} / ${total} j</strong> <small style="color:#64748B;">(${pct}%)</small>
                <div class="progress" style="height:6px; margin-top:4px; background:#E2E8F0; border-radius:3px; overflow:hidden;">
                  <div class="progress-bar" style="width:${pct}%; background:${color}; height:100%;"></div>
                </div>
              </div>`;
          }
        },
        { 
          data: 'montant_total_cotise', 
          className: 'text-end',
          render: (d) => `<strong style="color:#0F172A;">${Number(d || 0).toLocaleString('fr-FR')} FCFA</strong>` 
        },
        { 
          data: 'solde_restant', 
          className: 'text-end',
          render: function(d) {
            if ((d || 0) <= 0) return '<span style="color:#15803D; font-weight:800;">Soldé</span>';
            return `<strong style="color:#DC2626;">${Number(d).toLocaleString('fr-FR')} FCFA</strong>`;
          }
        },
        { data: 'date_souscription', defaultContent: '-', className: 'text-center' },
        { 
          data: null, 
          width: '250px', 
          orderable: false, 
          className: 'text-end',
          render: function(d) {
            const editId = d.editId || d.id_souscription;
            return `
              <a href="${racine}cautisation-payment/situation/${d.code_souscription}" class="btn btn-sm btn-success me-1" style="background:#15803D; border-color:#15803D;" title="Paiement">
                <i data-lucide="credit-card" style="width:14px;height:14px;"></i> Situation
              </a>
              <a href="${racine}souscription/edition/${editId}" class="btn btn-sm btn-secondary me-1">
                <i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer
              </a>
              <a href="${racine}souscription/details/${editId}" class="btn btn-sm btn-info">
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
