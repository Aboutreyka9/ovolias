$(document).ready(function() {
    function loadDashboard() {
        const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');

        $.getJSON(baseApi + 'home/dashboardData', function(resp) {
            const s = resp.stats || {};
            const p = s.pressing || null;

            // 1. Personnalisation du Header pour le pressing
            if (p && p.libelle_pressing) {
                $('#dash-title').text('Tableau de bord — ' + p.libelle_pressing);
                let metaHtml = '';
                if (p.adresse_pressing) {
                    metaHtml += '<span style="display:inline-flex; align-items:center; gap:6px; margin-right:16px;"><i class="fa fa-map-marker-alt" style="color:#2563EB;"></i> ' + p.adresse_pressing + '</span>';
                }
                if (p.telephone_pressing) {
                    metaHtml += '<span style="display:inline-flex; align-items:center; gap:6px;"><i class="fa fa-phone" style="color:#059669;"></i> <a href="tel:' + p.telephone_pressing + '" style="color:#64748B; text-decoration:none; font-weight:600;">' + p.telephone_pressing + '</a></span>';
                }
                if (metaHtml) {
                    $('#dash-subtitle').html(metaHtml);
                }
            } else if (s.is_pressing === false) {
                $('#dash-title').text('Tableau de bord — Supervision Globale');
                $('#dash-subtitle').html('<span style="display:inline-flex; align-items:center; gap:6px;"><i class="fa fa-globe" style="color:#2563EB;"></i> Vue d\'ensemble de tout le réseau de pressings partenaires</span>');
                $('#label-kpi-catalogue').text('Articles Réseau');
                $('#label-kpi-clients').text('Clients Réseau');
            }

            // 2. Remplissage des KPI Cards
            const caTotal = Number(s.ca_total || 0).toLocaleString('fr-FR') + ' FCFA';
            $('#kpi-ca').text(caTotal);
            $('#kpi-commandes').text(s.commandes || 0);
            $('#kpi-clients').text(s.clients || 0);
            $('#kpi-catalogue').text(s.tarifs || s.articles || 0);

            // 3. Remplissage du Pipeline d'Atelier
            $('#pipe-atraiter').text(s.a_traiter || 0);
            $('#pipe-traitement').text(s.en_traitement || 0);
            $('#pipe-pretes').text(s.pretes || 0);
            $('#pipe-livraison').text(s.en_livraison || 0);
            $('#pipe-livrees').text(s.livrees || 0);

            // 4. Remplissage du tableau des Commandes Récentes
            const tbody = $('#recentOrdersTable tbody');
            tbody.empty();

            if (!resp.recentOrders || resp.recentOrders.length === 0) {
                tbody.html('<tr><td colspan="7" style="text-align: center; padding: 30px; color: #94A3B8;">Aucune commande pour le moment.</td></tr>');
            } else {
                resp.recentOrders.forEach(function(o) {
                    const isColis = (o.type_commande === 'colis');
                    const typeBadge = isColis 
                        ? '<span style="background:#FEF3C7; color:#B45309; border:1px solid #FCD34D; border-radius:6px; padding:3px 7px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:3px;"><i class="fa fa-box"></i> Sac sans détail</span>'
                        : '<span style="background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE; border-radius:6px; padding:3px 7px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:3px;"><i class="fa fa-tshirt"></i> Détaillée</span>';

                    // Statut de suivi
                    const st = o.statut_suivi_commande || 'creee';
                    let statusClass = 'warning';
                    if (st === 'livree') statusClass = 'delivered';
                    else if (st === 'en_traitement' || st === 'prete' || st === 'en_livraison') statusClass = 'info';
                    else if (st === 'annulee') statusClass = 'cancelled';

                    const tr = $('<tr style="border-bottom: 1px solid #F1F5F9;">' +
                        '<td style="padding: 12px 16px; font-weight: 700; color: #1E293B;"><span class="code-badge">' + (o.code_commande || '') + '</span></td>' +
                        '<td style="padding: 12px 16px;">' + typeBadge + '</td>' +
                        '<td style="padding: 12px 16px;">' +
                            '<strong style="color: #334155; display: block;">' + (o.nom_client || 'Client') + '</strong>' +
                            '<small style="color: #64748B;">' + (o.telephone_client || '') + '</small>' +
                        '</td>' +
                        '<td style="padding: 12px 16px; text-align: right; font-weight: 700; color: #059669;">' + Number(o.montant_total_commande || 0).toLocaleString('fr-FR') + ' FCFA</td>' +
                        '<td style="padding: 12px 16px; text-align: center;"><span class="badge-status ' + statusClass + '" style="font-size: 11px; padding: 4px 8px;">' + (o.statutLabel || '') + '</span></td>' +
                        '<td style="padding: 12px 16px; color: #64748B; font-size: 13px;">' + (o.date_formatted || o.created_at_commande || '-') + '</td>' +
                        '<td style="padding: 12px 16px; text-align: center;">' +
                            '<a href="' + baseApi + 'commande/edition/' + o.editId + '" class="btn-action btn-action-primary" title="Gérer la commande">' +
                                '<i class="fa fa-eye"></i>' +
                            '</a>' +
                        '</td>' +
                    '</tr>');
                    tbody.append(tr);
                });

                // Vue Mobile
                if (window.innerWidth <= 768) {
                    var mobileContainer = $('#recentOrdersMobile');
                    var mobileHtml = '';
                    resp.recentOrders.forEach(function(o) {
                        const isColis = (o.type_commande === 'colis');
                        mobileHtml += '<div class="mobile-item">' +
                            '<div class="mobile-item-body">' +
                                '<div class="mobile-item-primary">' +
                                    '<span class="mobile-item-label">Commande</span>' +
                                    '<span class="mobile-item-value">' + (o.code_commande || '') + ' (' + (isColis ? 'Sac' : 'Détaillée') + ')</span>' +
                                '</div>' +
                                '<div class="mobile-item-primary">' +
                                    '<span class="mobile-item-label">Client</span>' +
                                    '<span class="mobile-item-value">' + (o.nom_client || '') + ' (' + (o.telephone_client || '') + ')</span>' +
                                '</div>' +
                                '<div class="mobile-item-meta">' +
                                    '<small><strong>' + Number(o.montant_total_commande || 0).toLocaleString('fr-FR') + ' FCFA</strong></small>' +
                                    '<small><span class="badge-status delivered">' + (o.statutLabel || '') + '</span></small>' +
                                    '<small>' + (o.date_formatted || '') + '</small>' +
                                '</div>' +
                            '</div>' +
                            '<a href="' + baseApi + 'commande/edition/' + o.editId + '" class="mobile-actions-toggle" aria-label="Gérer">' +
                                '<i data-lucide="chevron-right" style="width:18px;height:18px;"></i>' +
                            '</a>' +
                        '</div>';
                    });
                    mobileContainer.html(mobileHtml);
                } else {
                    $('#recentOrdersMobile').empty();
                }
            }

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // 5. Remplissage du solde portefeuille en direct
            if ($('#dash-wallet-balance').length) {
                $.getJSON(baseApi + 'retrait/apiSolde', function(r) {
                    if (r && r.status && r.data) {
                        const soldeDisp = Number(r.data.solde_disponible || 0).toLocaleString('fr-FR') + ' FCFA';
                        $('#dash-wallet-balance').text(soldeDisp);
                    }
                }).fail(function() {
                    $('#dash-wallet-balance').text('0 FCFA');
                });
            }
        });
    }

    loadDashboard();
});
