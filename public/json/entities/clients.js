window.openCommandeModal = function(clientCode) {
    const base = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');
    window.location.href = base + 'commande/list' + (clientCode ? '?client=' + encodeURIComponent(clientCode) : '');
};

$(document).ready(function() {
    const isSuperAdmin = (typeof window.IS_SUPER_ADMIN !== 'undefined' && window.IS_SUPER_ADMIN === true);
    const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');

    if ($('#dataTable').length) {
        const columns = [
            { 
                title: 'N°', 
                data: null, 
                render: function(data, type, row, meta) { return meta.row + 1; } 
            },
            { 
                data: 'code', 
                title: 'Code',
                render: function(data) {
                    return '<span class="code-badge">' + (data || '') + '</span>';
                }
            },
            { 
                data: 'nom', 
                title: 'Nom & Prénoms',
                render: function(data) {
                    return '<strong style="color: #1E293B;">' + (data || '') + '</strong>';
                }
            },
            { 
                data: 'telephone', 
                title: 'Téléphone',
                render: function(data) {
                    return '<span style="color: #64748B; font-weight: 600;"><i class="fa fa-phone"></i> ' + (data || '-') + '</span>';
                }
            },
            { 
                data: 'quartier', 
                title: 'Quartier',
                render: function(data) {
                    return '<span style="color: #2563EB;"><i class="fa fa-map-marker-alt"></i> ' + (data || '-') + '</span>';
                }
            },
            {
                data: 'statut',
                title: 'Statut',
                render: function(data) {
                    const isActif = (data === 'actif');
                    const cls = isActif ? 'delivered' : 'cancelled';
                    const label = isActif ? 'Actif' : 'Inactif';
                    return '<span class="badge-status ' + cls + '">' + label + '</span>';
                }
            },
            {
                data: null,
                title: 'Actions',
                className: 'text-center',
                render: function(data, type, row) {
                    // Si Super Admin : CONSULTATION EN LECTURE SEULE SEULEMENT
                    if (isSuperAdmin) {
                        return `
                            <div class="table-actions" style="justify-content: center;">
                                <a href="${baseApi}client/details/${row.editId}" title="Consulter la fiche client (Lecture seule)" class="btn-action btn-action-secondary">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </div>
                        `;
                    }

                    // Si Pressing Pro : Actions d'exploitation
                    const isActive = (row.statut === 'actif');
                    return `
                        <div class="table-actions">
                            <a href="${baseApi}client/details/${row.editId}" title="Voir fiche client" class="btn-action btn-action-secondary">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="${baseApi}client/edition/${row.editId}" title="Modifier le client" class="btn-action btn-action-primary">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" title="Nouvelle commande"
                                data-code="${row.code}"
                                class="btn-action btn-action-success btnNouvelleCommande">
                                <i class="fa fa-shopping-cart"></i>
                            </button>
                            <button type="button" title="${isActive ? 'Désactiver' : 'Activer'}"
                                data-id="${row.id}"
                                class="${isActive ? 'btn-action btn-action-warning changerStatus' : 'btn-action btn-action-success changerStatus'}">
                                <i class="fa ${isActive ? 'fa-toggle-on' : 'fa-toggle-off'}"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ];

        const table = initDataTable('dataTable', 'client/apiList', columns);

        if (!isSuperAdmin) {
            bindStatusToggle('dataTable', 'client/changer', table);
        }

        const mobileActions = [
            { id: 'voir', label: 'Voir fiche client', icon: 'eye', href: function(r) { return baseApi + 'client/details/' + r.editId; } }
        ];

        if (!isSuperAdmin) {
            mobileActions.push(
                { id: 'modifier', label: 'Modifier', icon: 'edit', href: function(r) { return baseApi + 'client/edition/' + r.editId; } },
                { id: 'nouvelle_commande', label: 'Nouvelle commande', icon: 'shopping-cart', onClick: function(rowData) {
                    window.openCommandeModal(rowData.code);
                }}
            );
        }

        const clientsMobileConfig = {
            entity: 'client',
            primary: [{ key: 'code', label: 'Code' }, { key: 'nom', label: 'Nom' }],
            secondary: [{ key: 'telephone', label: 'Téléphone' }, { key: 'quartier', label: 'Quartier' }],
            detailUrl: function(r) { return baseApi + 'client/details/' + r.editId; },
            actions: mobileActions,
            getActions: function(row) {
                var list = clientsMobileConfig.actions.map(function(a) { return Object.assign({}, a, { href: a.href ? a.href(row) : undefined }); });
                if (!isSuperAdmin) {
                    var isActive = row.statut === 'actif';
                    list.push({
                        id: isActive ? 'desactiver' : 'activer',
                        label: isActive ? 'Désactiver' : 'Activer',
                        icon: isActive ? 'toggle-left' : 'toggle-right',
                        onClick: function(rowData) {
                            showConfirm('Changer le statut de ce client ?', function() {
                                $.post(baseApi + 'client/changer', { id: rowData.id }, function(rep) {
                                    showToast(rep.message || 'Statut changé', rep.status ? 'success' : 'error');
                                    $('#dataTable').DataTable().ajax.reload();
                                }, 'json').fail(function() { showToast('Erreur serveur', 'error'); });
                            });
                        }
                    });
                }
                return list;
            }
        };
        renderMobileCards('dataTable', clientsMobileConfig);
    }

    $(document).on('click', '.btnNouvelleCommande', function() {
        const code = $(this).data('code');
        window.openCommandeModal(code);
    });

    $('.formEditClient').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btnEditClient');
        const alertBox = $('#editClientAlert');

        alertBox.hide().removeClass('alert-danger alert-success');

        const formData = form.serialize();
        const clientPhone = $('#telephone').val() ? $('#telephone').val().trim() : '';

        if (!clientPhone || clientPhone.length !== 10) {
            const err = 'Le numéro de téléphone doit contenir 10 chiffres !';
            alertBox.addClass('alert-danger').css('display', 'flex').find('#editClientAlertText').text(err);
            if (!alertBox.find('#editClientAlertText').length) {
                alertBox.html('<i class="fa fa-exclamation-circle"></i> ' + err);
            }
            showToast(err, 'error');
            return;
        }

        loading(btn, true, '<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

        const targetUrl = $('#id_client').val() ? (baseApi + 'client/edit') : (baseApi + 'client/add');

        $.ajax({
            url: targetUrl,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                loading(btn, false, '<i class="fa fa-save"></i> Sauvegarder');
                if (response.status) {
                    showToast(response.message, 'success');
                    setTimeout(function() {
                        window.location.href = baseApi + 'client/list';
                    }, 400);
                } else {
                    const msg = response.message || 'Erreur lors de la modification';
                    alertBox.addClass('alert-danger').css('display', 'flex');
                    if (alertBox.find('#editClientAlertText').length) {
                        alertBox.find('#editClientAlertText').text(msg);
                    } else {
                        alertBox.html('<i class="fa fa-exclamation-circle"></i> ' + msg);
                    }
                    showToast(msg, 'error');
                }
            },
            error: function(xhr) {
                loading(btn, false, '<i class="fa fa-save"></i> Sauvegarder');
                let msg = 'Erreur lors de l\'enregistrement';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alertBox.addClass('alert-danger').css('display', 'flex');
                if (alertBox.find('#editClientAlertText').length) {
                    alertBox.find('#editClientAlertText').text(msg);
                } else {
                    alertBox.html('<i class="fa fa-exclamation-circle"></i> ' + msg);
                }
                showToast(msg, 'error');
            }
        });
    });
});
