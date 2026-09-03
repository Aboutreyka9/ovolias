/**
 * Module Caisse Commercial - Olive Service
 * Registre des caisses journalières pour commerciaux et validation comptable
 */

$(function() {
  const racine = window.AppConfig ? window.AppConfig.racine : (window.RACINE || '/');
  const csrfToken = $('#csrf_token').val() || '';
  const isCommercial = window.AppConfig ? window.AppConfig.isCommercial : false;

  // --- PARTIE 1 : HISTORIQUE DES CAISSES (LISTE) ---
  const $tableClo = $('#table-clotures_caisse');
  if ($tableClo.length) {
    const table = $tableClo.DataTable({
      ajax: racine + 'caisse_commercial/apiList',
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
              <a href="${racine}caisse_commercial/details/${editId}" class="btn btn-sm btn-info me-1">
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
        url: racine + 'caisse_commercial/changer',
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

  // --- PARTIE 2 : TABLEAU DE BORD COMMERCIAL & CLÔTURE (EDIT) ---
  const $caisseLoader = $('#caisse-loader');
  if ($caisseLoader.length) {
    let currentCotisations = [];
    const $modalCotisations = $('#modalCotisationsDetails');

    function loadCommercialSession() {
      $caisseLoader.show();
      $('#section-caisse-ouverte').hide();
      $('#section-caisse-fermee').hide();

      $.ajax({
        url: racine + 'caisse_commercial/apiGetCommercialSession',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
          $caisseLoader.hide();

          if (res.status === 1) {
            if (res.has_active_session) {
              // CAISSE OUVERTE
              const s = res.session;
              currentCotisations = s.cotisations || [];

              $('#open_session_code').text(s.code_caisse);
              $('#open_session_date').text(s.date_caisse);
              $('#open_session_heure').text(s.heure_ouverture || '-');

              $('#open_total_general').text(s.total_general_fmt);
              $('#open_nb_cotisations').text(s.nb_cotisations);
              $('#open_fond_initial').text(s.fond_initial_fmt);

              $('#open_especes').text(Number(s.total_especes).toLocaleString('fr-FR') + ' FCFA');
              $('#open_mobile').text(Number(s.total_mobile_money).toLocaleString('fr-FR') + ' FCFA');
              $('#open_cheques').text(Number(s.total_cheque_virement).toLocaleString('fr-FR') + ' FCFA');

              renderCotisationsTable(currentCotisations);
              $('#section-caisse-ouverte').fadeIn(200);
            } else {
              // CAISSE FERMÉE
              if (res.last_cloture) {
                const l = res.last_cloture;
                currentCotisations = l.cotisations || [];
                renderCotisationsTable(currentCotisations);

                $('#last_code_lbl').text(l.code_cloture);
                $('#last_date_lbl').text(l.date_cloture);
                $('#last_total_lbl').text(l.total_general_fmt);

                let badgeStatut = '';
                if (l.statut_cloture === 'valide') {
                  badgeStatut = '<span style="background: #DCFCE7; color: #15803D; font-weight: 800; font-size: 12px; padding: 4px 10px; border-radius: 12px;">Validée par Finance</span>';
                } else if (l.statut_cloture === 'rejete') {
                  badgeStatut = '<span style="background: #FEE2E2; color: #B91C1C; font-weight: 800; font-size: 12px; padding: 4px 10px; border-radius: 12px;">Rejetée</span>';
                } else {
                  badgeStatut = '<span style="background: #FEF3C7; color: #B45309; font-weight: 800; font-size: 12px; padding: 4px 10px; border-radius: 12px;">En attente de validation</span>';
                }
                $('#last_statut_lbl').html(badgeStatut);

                $('#card-dernier-bilan').show();
              }

              $('#section-caisse-fermee').fadeIn(200);
            }

            if (window.lucide) lucide.createIcons();
          }
        },
        error: function() {
          $caisseLoader.hide();
          $('#section-caisse-fermee').show();
          if (window.toastr) toastr.error('Erreur réseau lors de la récupération de votre session de caisse.');
        }
      });
    }

    function renderCotisationsTable(list) {
      const $tbody = $('#tbl_cotisations_body');
      $tbody.empty();

      if (!list || list.length === 0) {
        $tbody.html('<tr><td colspan="7" style="text-align: center; color: #94A3B8; padding: 26px; font-weight: 600;">Aucune cotisation enregistrée dans cette caisse.</td></tr>');
        return;
      }

      list.forEach(function(item) {
        const isValide = (item.statut === 'valide');
        const badge = isValide 
          ? '<span style="background: #DCFCE7; color: #166534; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; border: 1px solid #86EFAC;">Validée</span>'
          : '<span style="background: #FEF3C7; color: #B45309; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; border: 1px solid #FCD34D;">En attente</span>';

        const modeBadge = `<span style="background: #F1F5F9; color: #334155; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px; border: 1px solid #CBD5E1;">${item.mode_paiement || 'ESPECES'}</span>`;

        $tbody.append(`
          <tr style="border-bottom: 1px solid #F1F5F9;">
            <td style="padding: 12px 14px;"><code style="font-weight:700; color:#1E3A5F; font-size: 12px;">${item.code_souscription || '-'}</code></td>
            <td style="padding: 12px 14px;"><strong style="color: #0F172A; font-size: 13px;">${item.nom_client || 'Client'}</strong><br><small style="color:#64748B;">${item.telephone_client || '-'}</small></td>
            <td style="padding: 12px 14px;"><strong style="color:#15803D; font-size: 13px;">${item.montant_fmt || '0 FCFA'}</strong></td>
            <td style="padding: 12px 14px;">${modeBadge}</td>
            <td style="padding: 12px 14px; color: #475569;">${item.date_cautisation || '-'}</td>
            <td style="padding: 12px 14px;"><strong style="color:#0F172A;">${item.date_prochain_rdv || '-'}</strong></td>
            <td style="padding: 12px 14px;">${badge}</td>
          </tr>
        `);
      });
    }

    // Action Ouverture de Caisse
    $('#form-ouvrir-caisse').on('submit', function(e) {
      e.preventDefault();
      const formData = $(this).serialize();

      $.ajax({
        url: racine + 'caisse_commercial/ouvrirMaCaisse',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(res) {
          if (res.status === 1 || res.success) {
            if (window.toastr) toastr.success(res.message || 'Caisse ouverte avec succès !');
            loadCommercialSession();
          } else {
            if (window.toastr) toastr.error(res.message || 'Erreur lors de l\'ouverture');
          }
        },
        error: function() {
          if (window.toastr) toastr.error('Erreur réseau');
        }
      });
    });

    // Action Clôture de Caisse
    $('#form-cloturer-caisse').on('submit', function(e) {
      e.preventDefault();
      const formData = $(this).serialize();

      $.ajax({
        url: racine + 'caisse_commercial/add',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(res) {
          if (res.status === 1 || res.success) {
            if (window.toastr) toastr.success(res.message || 'Clôture transmise avec succès !');
            loadCommercialSession();
          } else {
            if (window.toastr) toastr.error(res.message || 'Erreur lors de la clôture');
          }
        },
        error: function() {
          if (window.toastr) toastr.error('Erreur réseau');
        }
      });
    });

    // GESTION DU DÉCLENCHEMENT DE LA MODALE
    $(document).on('click', '.btn-open-modal-details', function(e) {
      e.preventDefault();
      $modalCotisations.addClass('active');
      if (window.lucide) lucide.createIcons();
    });

    $('#modalCotisationsClose, #btn-close-modal-cotisations').on('click', function(e) {
      e.preventDefault();
      $modalCotisations.removeClass('active');
    });

    $modalCotisations.on('click', function(e) {
      if ($(e.target).is('#modalCotisationsDetails')) {
        $modalCotisations.removeClass('active');
      }
    });

    // Charge la session initiale
    loadCommercialSession();
  }
});
