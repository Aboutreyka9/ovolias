let currentAbnList = [];
let activeSubData = null;

function openCreateAbonnementModal() {
    const modal = document.getElementById('modal-creer-abonnement');
    if (modal) {
        modal.style.display = 'flex';
        $('#force_replace').val('0');
        $('#alert-abonnement-existant').hide();
        recalcCreateDates();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

function closeCreateAbonnementModal() {
    const modal = document.getElementById('modal-creer-abonnement');
    if (modal) modal.style.display = 'none';
}

function checkPressingActiveSub(pressingCode) {
    if (!pressingCode) {
        $('#alert-abonnement-existant').hide();
        $('#force_replace').val('0');
        return;
    }

    const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');
    $.post(baseApi + 'abonnement/checkActive', { pressing_code: pressingCode }, function(resp) {
        if (resp && resp.has_active && resp.abonnement) {
            activeSubData = resp.abonnement;
            const msg = 'Ce pressing bénéficie actuellement du forfait <strong>' + (activeSubData.forfait || '') + '</strong> ' +
                        'valide jusqu\'au <strong>' + (activeSubData.date_fin || '') + '</strong> ' +
                        '(<strong>' + (activeSubData.jours_restants || 0) + ' jour(s) restant(s)</strong>).';
            $('#alert-existant-msg').html(msg);
            $('#alert-abonnement-existant').slideDown(200);

            $('#btn-redirect-renouveler').off('click').on('click', function() {
                closeCreateAbonnementModal();
                openRenouvelerModal(activeSubData);
            });
        } else {
            activeSubData = null;
            $('#alert-abonnement-existant').slideUp(200);
            $('#force_replace').val('0');
        }
    }, 'json');
}

function confirmForceReplace() {
    $('#force_replace').val('1');
    $('#alert-abonnement-existant').html(
        '<div style="color: #059669; font-weight: 700; font-size: 13px; display: flex; align-items: center; gap: 6px;">' +
        '<i class="fa fa-check-circle"></i> Option confirmée : L\'ancien forfait sera archivé et remplacé par le nouveau dès l\'activation.' +
        '</div>'
    );
}

function onForfaitChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (opt) {
        const montant = parseFloat(opt.getAttribute('data-montant')) || 0;
        const duree = parseInt(opt.getAttribute('data-duree')) || 1;
        $('#abn_duree_mois').val(duree);
        $('#abn_montant').val(montant);
        recalcCreateDates();
    }
}

function recalcCreateDates() {
    const dateDebutVal = $('#abn_date_debut').val() || new Date().toISOString().split('T')[0];
    const duree = parseInt($('#abn_duree_mois').val()) || 1;
    
    const d = new Date(dateDebutVal);
    d.setMonth(d.getMonth() + duree);
    const dateFinStr = d.toISOString().split('T')[0];
    $('#abn_date_fin').val(dateFinStr);
}

function openRenouvelerModal(row) {
    const modal = document.getElementById('modal-renouveler-abonnement');
    if (!modal) return;

    $('#renouv_id_abonnement').val(row.id);
    $('#renouv_current_date_fin').val(row.date_fin_raw || new Date().toISOString().split('T')[0]);
    $('#renouv-abn-code').text(row.code);
    $('#renouv-pressing-name').text(row.pressing);
    $('#renouv-date-fin-actuelle').text(row.date_fin);

    if (row.forfait_code) {
        $('#renouv_forfait_code').val(row.forfait_code).trigger('change');
    } else {
        recalcRenouvDates();
    }

    modal.style.display = 'flex';
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeRenouvelerModal() {
    const modal = document.getElementById('modal-renouveler-abonnement');
    if (modal) modal.style.display = 'none';
}

function onRenouvForfaitChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (opt) {
        const montant = parseFloat(opt.getAttribute('data-montant')) || 0;
        const duree = parseInt(opt.getAttribute('data-duree')) || 1;
        $('#renouv_duree_mois').val(duree);
        $('#renouv_montant').val(montant);
        recalcRenouvDates();
    }
}

function recalcRenouvDates() {
    const curDateFin = $('#renouv_current_date_fin').val() || new Date().toISOString().split('T')[0];
    const duree = parseInt($('#renouv_duree_mois').val()) || 1;

    let base = new Date(curDateFin);
    const today = new Date();
    today.setHours(0,0,0,0);

    // Si l'abonnement est déjà expiré, on repart d'aujourd'hui
    if (base < today) {
        base = new Date();
    }

    base.setMonth(base.getMonth() + duree);
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    const dateFormatted = base.toLocaleDateString('fr-FR', options);
    $('#renouv-nouvelle-date-fin').text(dateFormatted);
}

function submitCreateAbonnement(e) {
    e.preventDefault();
    const form = $(e.target);
    const btn = form.find('.btnSubmitCreateAbn');
    const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');

    if (typeof loading === 'function') {
        loading(btn, true, '<i class="fa fa-spinner fa-spin"></i> Activation...');
    }

    $.ajax({
        url: baseApi + 'abonnement/add',
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(rep) {
            if (typeof loading === 'function') {
                loading(btn, false, '<i data-lucide="check-circle"></i> Activer l\'abonnement');
            }
            if (rep.status) {
                if (typeof showToast === 'function') showToast(rep.message || 'Abonnement activé avec succès !', 'success');
                closeCreateAbonnementModal();
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#dataTable')) {
                    $('#dataTable').DataTable().ajax.reload();
                } else {
                    setTimeout(() => window.location.reload(), 700);
                }
            } else if (rep.has_active) {
                if (typeof showToast === 'function') showToast(rep.message, 'warning');
                $('#alert-abonnement-existant').show();
            } else {
                if (typeof showToast === 'function') showToast(rep.message || 'Erreur lors de la création', 'error');
            }
        },
        error: function(xhr) {
            if (typeof loading === 'function') {
                loading(btn, false, '<i data-lucide="check-circle"></i> Activer l\'abonnement');
            }
            let msg = 'Erreur lors de la création de l\'abonnement';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            if (typeof showToast === 'function') showToast(msg, 'error');
        }
    });
}

function submitRenouvelerAbonnement(e) {
    e.preventDefault();
    const form = $(e.target);
    const btn = form.find('.btnSubmitRenouv');
    const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');

    if (typeof loading === 'function') {
        loading(btn, true, '<i class="fa fa-spinner fa-spin"></i> Renouvellement...');
    }

    $.ajax({
        url: baseApi + 'abonnement/renouveler',
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(rep) {
            if (typeof loading === 'function') {
                loading(btn, false, '<i data-lucide="refresh-cw"></i> Confirmer le renouvellement');
            }
            if (rep.status) {
                if (typeof showToast === 'function') showToast(rep.message || 'Abonnement renouvelé avec succès !', 'success');
                closeRenouvelerModal();
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#dataTable')) {
                    $('#dataTable').DataTable().ajax.reload();
                } else {
                    setTimeout(() => window.location.reload(), 700);
                }
            } else {
                if (typeof showToast === 'function') showToast(rep.message || 'Erreur lors du renouvellement', 'error');
            }
        },
        error: function(xhr) {
            if (typeof loading === 'function') {
                loading(btn, false, '<i data-lucide="refresh-cw"></i> Confirmer le renouvellement');
            }
            let msg = 'Erreur lors du renouvellement';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            if (typeof showToast === 'function') showToast(msg, 'error');
        }
    });
}

$(document).ready(function() {
    if ($('#dataTable').length) {
        const columns = [
            { title: 'N°', data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
            { 
                data: 'code', 
                title: 'Code',
                render: function(data) {
                    return '<span class="code-badge">' + data + '</span>';
                }
            },
            { 
                data: 'pressing', 
                title: 'Pressing',
                render: function(data) {
                    return '<strong style="color:#1E293B;">' + data + '</strong>';
                }
            },
            { 
                data: 'forfait', 
                title: 'Forfait B2B',
                render: function(data) {
                    return '<span style="font-weight:700; color:#1E3A5F; background:#EFF6FF; padding:4px 9px; border-radius:6px; border:1px solid #BFDBFE;">' + data + '</span>';
                }
            },
            { 
                data: 'montant', 
                title: 'Montant',
                render: function(data) {
                    return '<strong style="color:#059669; font-size:14px;">' + Number(data).toLocaleString('fr-FR') + ' FCFA</strong>';
                }
            },
            {
                data: null,
                title: 'Période & Validité',
                render: function(data, type, row) {
                    let html = '<div style="font-size:13px; color:#334155;">Du ' + (row.date_debut || '-') + ' au ' + (row.date_fin || '-') + '</div>';
                    if (row.jours_restants !== null && row.jours_restants !== undefined) {
                        if (row.jours_restants > 0) {
                            html += '<small style="color:#059669; font-weight:700;"><i class="fa fa-clock"></i> ' + row.jours_restants + ' jour(s) restant(s)</small>';
                        } else {
                            html += '<small style="color:#DC2626; font-weight:700;"><i class="fa fa-exclamation-triangle"></i> Expiré</small>';
                        }
                    }
                    return html;
                }
            },
            {
                data: 'statut',
                title: 'Statut',
                render: function(data) {
                    let cls = 'delivered';
                    let label = 'Actif';
                    if (data === 'expire') {
                        cls = 'cancelled';
                        label = 'Expiré';
                    } else if (data === 'suspendu') {
                        cls = 'warning';
                        label = 'Suspendu';
                    }
                    return '<span class="badge-status ' + cls + '">' + label + '</span>';
                }
            },
            {
                data: null,
                title: 'Actions',
                className: 'text-center',
                render: function(data, type, row) {
                    const isActif = (row.statut === 'actif');
                    return `
                        <div class="table-actions">
                            <button type="button" class="btn-action btn-action-success btnRenouvelerAbn" 
                                    data-row='${JSON.stringify(row)}' title="Renouveler l'abonnement">
                                <i class="fa fa-sync"></i>
                            </button>
                            <button type="button" class="btn-action ${isActif ? 'btn-action-warning' : 'btn-action-primary'} btnToggleAbnStatut"
                                    data-id="${row.id}" title="${isActif ? 'Suspendre' : 'Activer'}">
                                <i class="fa ${isActif ? 'fa-pause' : 'fa-play'}"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ];

        const table = initDataTable('dataTable', 'abonnement/apiList', columns);

        $(document).on('click', '.btnRenouvelerAbn', function() {
            const rowData = $(this).data('row');
            openRenouvelerModal(rowData);
        });

        $(document).on('click', '.btnToggleAbnStatut', function() {
            const id = $(this).data('id');
            const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');
            showConfirm('Voulez-vous modifier le statut de cet abonnement ?', function() {
                $.post(baseApi + 'abonnement/changer', { id: id }, function(rep) {
                    if (typeof showToast === 'function') showToast(rep.message || 'Statut mis à jour', rep.status ? 'success' : 'error');
                    table.ajax.reload();
                }, 'json').fail(function() {
                    if (typeof showToast === 'function') showToast('Erreur serveur', 'error');
                });
            }, 'Statut Abonnement', 'Modifier', false);
        });

        const abonnementsMobileConfig = {
            entity: 'abonnement',
            primary: [{ key: 'pressing', label: 'Pressing' }, { key: 'forfait', label: 'Forfait' }],
            secondary: [{ key: 'code', label: 'Code' }, { key: 'statut', label: 'Statut' }],
            detailUrl: function(r) { return '#'; },
            actions: []
        };
        renderMobileCards('dataTable', abonnementsMobileConfig);
    }
});
