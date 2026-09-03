$(document).ready(function() {
    const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');

    if ($('#dataTable').length) {
        const columns = [
            { 
                title: 'N°', 
                data: null, 
                render: function(data, type, row, meta) { return meta.row + 1; } 
            },
            { 
                data: 'type', 
                title: 'Type',
                render: function(data) {
                    let icon = 'fa-bell';
                    let bg = '#EFF6FF';
                    let color = '#2563EB';
                    let label = data || 'Système';

                    if (data && data.startsWith('commande')) {
                        icon = 'fa-shopping-bag';
                        bg = '#ECFDF5';
                        color = '#059669';
                    } else if (data && data.startsWith('paiement')) {
                        icon = 'fa-credit-card';
                        bg = '#FEF3C7';
                        color = '#D97706';
                    } else if (data === 'alerte') {
                        icon = 'fa-bullhorn';
                        bg = '#FEE2E2';
                        color = '#DC2626';
                    }

                    return `
                        <span style="background:${bg}; color:${color}; border:1px solid ${color}40; font-size:12px; font-weight:700; padding:4px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;">
                            <i class="fa ${icon}"></i> ${label}
                        </span>
                    `;
                }
            },
            { 
                data: 'client_nom', 
                title: 'Destinataire',
                render: function(data, type, row) {
                    const phone = row.client_telephone ? `<small style="color:#64748B; display:block;"><i class="fa fa-phone"></i> ${row.client_telephone}</small>` : '';
                    return `
                        <div>
                            <strong style="color:#1E293B; font-size:13px;">${data || 'Tous les clients'}</strong>
                            ${phone}
                        </div>
                    `;
                }
            },
            { 
                data: 'titre', 
                title: 'Titre & Message',
                render: function(data, type, row) {
                    const isUnread = (parseInt(row.lu) === 0);
                    const dot = isUnread ? '<span style="width:8px; height:8px; border-radius:50%; background:#2563EB; display:inline-block; margin-right:6px;" title="Non lue"></span>' : '';
                    return `
                        <div style="max-width:360px;">
                            <strong style="color:${isUnread ? '#1E293B' : '#475569'}; font-size:13px; display:flex; align-items:center;">
                                ${dot}${data || ''}
                            </strong>
                            <p style="color:#64748B; font-size:12px; margin:2px 0 0 0; line-height:1.3;">
                                ${row.message || ''}
                            </p>
                        </div>
                    `;
                }
            },
            { 
                data: 'reference', 
                title: 'Réf. Associée',
                render: function(data) {
                    if (!data) return '<span style="color:#94A3B8;">-</span>';
                    return `<span class="code-badge">${data}</span>`;
                }
            },
            { 
                data: 'created_at', 
                title: 'Date & Heure',
                render: function(data) {
                    return `<span style="color:#64748B; font-size:12px;"><i class="fa fa-clock"></i> ${data || '-'}</span>`;
                }
            },
            { 
                data: 'lu', 
                title: 'État',
                render: function(data) {
                    const isLu = (parseInt(data) === 1);
                    if (isLu) {
                        return '<span class="badge-status delivered" style="font-size:11px;"><i class="fa fa-check"></i> Lue</span>';
                    } else {
                        return '<span class="badge-status in-progress" style="font-size:11px;"><i class="fa fa-envelope"></i> Non lue</span>';
                    }
                }
            },
            {
                data: null,
                title: 'Actions',
                className: 'text-center',
                render: function(data, type, row) {
                    const isLu = (parseInt(row.lu) === 1);
                    return `
                        <div class="table-actions">
                            <button type="button" title="${isLu ? 'Marquer comme non lue' : 'Marquer comme lue'}"
                                data-id="${row.id}"
                                class="btn-action ${isLu ? 'btn-action-secondary' : 'btn-action-primary'} toggleReadStatus">
                                <i class="fa ${isLu ? 'fa-envelope-open' : 'fa-check'}"></i>
                            </button>
                            <button type="button" title="Supprimer"
                                data-id="${row.id}"
                                class="btn-action btn-action-danger deleteNotifBtn">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ];

        const table = initDataTable('dataTable', 'notification/apiList', columns);

        // Toggle Read Status
        $(document).on('click', '.toggleReadStatus', function() {
            const id = $(this).attr('data-id');
            if (!id) return;

            $.post(baseApi + 'notification/changer', { id: id }, function(rep) {
                if (rep.status) {
                    if (typeof showToast === 'function') showToast(rep.message || 'Statut mis à jour', 'success');
                    table.ajax.reload(null, false);
                    reloadStats();
                } else {
                    if (typeof showToast === 'function') showToast(rep.message || 'Erreur', 'error');
                }
            }, 'json').fail(function() {
                if (typeof showToast === 'function') showToast('Erreur serveur', 'error');
            });
        });

        // Delete Notification
        $(document).on('click', '.deleteNotifBtn', function() {
            const id = $(this).attr('data-id');
            if (!id) return;

            showConfirm('Voulez-vous vraiment supprimer cette notification ?', function() {
                $.post(baseApi + 'notification/delete', { id: id }, function(rep) {
                    if (rep.status) {
                        if (typeof showToast === 'function') showToast(rep.message || 'Notification supprimée', 'success');
                        table.ajax.reload(null, false);
                        reloadStats();
                    } else {
                        if (typeof showToast === 'function') showToast(rep.message || 'Erreur', 'error');
                    }
                }, 'json').fail(function() {
                    if (typeof showToast === 'function') showToast('Erreur serveur', 'error');
                });
            }, 'Suppression', 'Supprimer', true);
        });

        // Mobile list config
        const notifsMobileConfig = {
            entity: 'notification',
            primary: [{ key: 'titre', label: 'Titre' }, { key: 'client_nom', label: 'Destinataire' }],
            secondary: [{ key: 'type', label: 'Type' }, { key: 'created_at', label: 'Date' }],
            actions: [
                {
                    id: 'toggle',
                    label: 'Changer état de lecture',
                    icon: 'check',
                    onClick: function(rowData) {
                        $.post(baseApi + 'notification/changer', { id: rowData.id }, function(rep) {
                            if (typeof showToast === 'function') showToast(rep.message, 'success');
                            table.ajax.reload(null, false);
                            reloadStats();
                        }, 'json');
                    }
                }
            ]
        };
        renderMobileCards('dataTable', notifsMobileConfig);
    }

    // Formulaire d'envoi de notification
    $('#formSendNotification').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('button[type="submit"]');

        if (typeof loading === 'function') loading(btn, true, 'Envoi en cours...');

        $.ajax({
            url: baseApi + 'notification/add',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                if (typeof loading === 'function') loading(btn, false);
                if (rep.status) {
                    if (typeof showToast === 'function') showToast(rep.message || 'Notification diffusée avec succès !', 'success');
                    closeSendModal();
                    form[0].reset();
                    if ($('#dataTable').length) {
                        $('#dataTable').DataTable().ajax.reload(null, false);
                    }
                    reloadStats();
                } else {
                    if (typeof showToast === 'function') showToast(rep.message || 'Erreur lors de l\'envoi', 'error');
                }
            },
            error: function(xhr) {
                if (typeof loading === 'function') loading(btn, false);
                let msg = 'Erreur lors de l\'envoi';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (typeof showToast === 'function') showToast(msg, 'error');
            }
        });
    });

    // Fonction recharger les statistiques KPI
    function reloadStats() {
        $.get(baseApi + 'notification/stats', function(data) {
            if (data) {
                $('#kpi-total').text(data.total || 0);
                $('#kpi-nonlues').text(data.non_lues || 0);
                $('#kpi-lues').text(data.lues || 0);
                $('#kpi-commandes').text(data.commandes || 0);
            }
        }, 'json');
    }
});

// Fonctions globales
function openSendModal() {
    $('#modalSendNotification').css('display', 'flex');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeSendModal() {
    $('#modalSendNotification').hide();
}

function markAllNotificationsRead() {
    const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');
    showConfirm('Voulez-vous marquer toutes les notifications comme lues ?', function() {
        $.post(baseApi + 'notification/marquerToutLu', {}, function(rep) {
            if (rep.status) {
                if (typeof showToast === 'function') showToast(rep.message || 'Toutes les notifications sont marquées comme lues', 'success');
                if ($('#dataTable').length) {
                    $('#dataTable').DataTable().ajax.reload(null, false);
                }
                $.get(baseApi + 'notification/stats', function(data) {
                    if (data) {
                        $('#kpi-nonlues').text(data.non_lues || 0);
                        $('#kpi-lues').text(data.lues || 0);
                    }
                }, 'json');
            } else {
                if (typeof showToast === 'function') showToast(rep.message || 'Erreur', 'error');
            }
        }, 'json').fail(function() {
            if (typeof showToast === 'function') showToast('Erreur serveur', 'error');
        });
    }, 'Notifications', 'Tout marquer comme lu', false);
}
