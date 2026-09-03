<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php $currentSection = 'cautisation'; ?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">

      <!-- EN-TÊTE DE PAGE -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="search" style="color: #1E3A5F; width: 26px; height: 26px;"></i>
            <span>Recherche de Souscription pour Paiement</span>
          </h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Consultation et paiement des cautisations clients</p>
        </div>
        <div style="display: flex; gap: 10px;">
          <button type="button" id="btnRefresh" class="btn btn-secondary" style="background: #F1F5F9; border-color: #CBD5E1; color: #334155; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i> Actualiser
          </button>
          <a href="<?= RACINE ?>souscription/wizard" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="plus" style="width: 16px; height: 16px;"></i> Nouvelle souscription
          </a>
        </div>
      </div>

      <!-- FORMULAIRE DE RECHERCHE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box; margin-bottom: 24px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; align-items: end;">
          <div>
            <label style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Critère de recherche</label>
            <input type="text" id="searchInput" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" placeholder="Téléphone, nom, code client ou code souscription" autocomplete="off">
          </div>
          <div>
            <label style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Type de recherche</label>
            <select id="searchType" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none; background: white;">
              <option value="all">Tous les critères</option>
              <option value="phone">Téléphone</option>
              <option value="name">Nom du client</option>
              <option value="code">Code client</option>
              <option value="subscription">Code souscription</option>
            </select>
          </div>
          <div>
            <button type="button" id="searchBtn" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; color: white; width: 100%; font-weight: 700; border-radius: 8px; padding: 11px 14px; font-size: 14px;">
              <i data-lucide="search" style="width: 16px; height: 16px;"></i> Rechercher
            </button>
          </div>
        </div>
      </div>

      <!-- RÉSULTATS DE RECHERCHE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <div id="searchPlaceholder" style="text-align: center; padding: 40px 20px; color: #94A3B8;">
          <i data-lucide="search" style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
          <p style="font-size: 14px; margin: 0;">Entrez un critère de recherche et cliquez sur <strong>Rechercher</strong> pour afficher les souscriptions.</p>
        </div>
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; display: none;" id="searchResultsContainer">
          <table id="table-search-results" class="table display nowrap" style="width:100%; max-width:100%; border-collapse: collapse;">
            <thead>
              <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                <th style="padding: 12px;">Client</th>
                <th style="padding: 12px;">Téléphone</th>
                <th style="padding: 12px;">Code Souscription</th>
                <th style="padding: 12px;">Session</th>
                <th style="padding: 12px; text-align: right;">Montant Total</th>
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
  if (window.lucide) lucide.createIcons();

  const searchBtn = document.getElementById('searchBtn');
  const searchInput = document.getElementById('searchInput');
  const searchType = document.getElementById('searchType');
  const searchResultsContainer = document.getElementById('searchResultsContainer');
  const searchPlaceholder = document.getElementById('searchPlaceholder');
  let dataTable = null;

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function formatCurrency(amount) {
    return Number(amount || 0).toLocaleString('fr-FR') + ' FCFA';
  }

  function renderStatut(statut) {
    const map = {
      'valide':   { badge: 'bg-success', label: 'Validée' },
      'solde':    { badge: 'bg-info', label: 'Soldée' },
      'annule':   { badge: 'bg-danger', label: 'Annulée' },
      'reconduite': { badge: 'bg-warning text-dark', label: 'Reconduite' }
    };
    const m = map[statut] || { badge: 'bg-secondary', label: statut || '-' };
    return '<span class="badge ' + m.badge + '" style="font-size:11px; padding:4px 10px; border-radius:6px;">' + m.label + '</span>';
  }

  function performSearch() {
    const criteria = searchInput.value.trim();
    const type = searchType.value;

    if (!criteria) {
      toastr.error('Veuillez entrer un critère de recherche.');
      return;
    }

    if (dataTable) {
      dataTable.destroy();
      dataTable = null;
    }

    searchPlaceholder.style.display = 'none';
    searchResultsContainer.style.display = 'block';

    const table = $('#table-search-results').DataTable({
      ajax: {
        url: '<?= RACINE ?>cautisation-payment/search',
        type: 'POST',
        data: function(d) {
          d.criteria = criteria;
          d.type = type;
        },
        dataSrc: function(json) {
          if (json.error) {
            toastr.error(json.error);
            return [];
          }
          if (json.message) {
            toastr.info(json.message);
            return [];
          }
          return json.data || [];
        },
        error: function() {
          toastr.error('Erreur réseau lors de la recherche.');
          return [];
        }
      },
      processing: true,
      autoWidth: false,
      columns: [
        { data: 'nom_complet', render: function(d) {
            return '<strong style="color:#0F172A;">' + escapeHtml(d || '-') + '</strong>';
        }},
        { data: 'telephone', defaultContent: '-' },
        { data: 'code_souscription', render: function(d) {
            return d ? '<code style="font-weight:700; color:#334155; background:#F1F5F9; padding:2px 6px; border-radius:4px;">' + escapeHtml(d) + '</code>' : '-';
        }},
        { data: 'libelle_session', defaultContent: '-' },
        { data: 'montant_total', render: function(d) {
            return '<strong style="color:#15803D;">' + formatCurrency(d) + '</strong>';
        }},
        { data: 'statut', className: 'text-center', render: function(d) {
            return renderStatut(d);
        }},
        { data: null, width: '120px', orderable: false, className: 'text-end', render: function(d) {
            return '<a href="<?= RACINE ?>cautisation-payment/situation?code=' + escapeHtml(d.code_sousscription || d.code_souscription || '') + '" class="btn btn-sm" style="background: #1E3A5F; color: white; border: none; border-radius: 6px; padding: 6px 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;"><i data-lucide="eye" style="width:14px; height:14px;"></i> Voir</a>';
        }}
      ],
      language: { url: '<?= RACINE ?>json/datatables-i18n-fr-FR.json' },
      drawCallback: function() { if (window.lucide) lucide.createIcons(); }
    });
    dataTable = $('#table-search-results').DataTable();
  }

  searchBtn.addEventListener('click', performSearch);
  searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') performSearch();
  });

  document.getElementById('btnRefresh').addEventListener('click', function() {
    searchInput.value = '';
    if (dataTable) {
      dataTable.destroy();
      dataTable = null;
    }
    searchPlaceholder.style.display = 'block';
    searchResultsContainer.style.display = 'none';
  });
});
</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
