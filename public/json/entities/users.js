$(document).ready(function() {
    if ($('#dataTable').length) {
        const columns = [
            { title: 'N°', data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
            { data: 'code', title: 'Code' },
            { data: 'nom', title: 'Nom' },
            { data: 'prenom', title: 'Prénom' },
            { data: 'telephone', title: 'Téléphone' },
            { data: 'role', title: 'Rôle' },
            {
                data: 'statut',
                title: 'Statut',
                render: function(data) {
                    const cls = data === 'actif' ? 'delivered' : 'cancelled';
                    return '<span class="badge-status ' + cls + '">' + data + '</span>';
                }
            },
            {
                data: null,
                title: 'Actions',
                className: 'text-center',
                render: function(data, type, row) {
                    const isActive = row.statut === 'actif';
                    return `
                        <div class="table-actions">
                            <a href="${LINK}user/details/${row.editId}" title="Voir" class="btn-action btn-action-secondary"><i class="fa fa-eye"></i></a>
                            <a href="${LINK}user/edition/${row.editId}" title="Modifier" class="btn-action btn-action-primary"><i class="fa fa-edit"></i></a>
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

        const table = initDataTable('dataTable', 'user/apiList', columns);
        bindStatusToggle('dataTable', 'user/changer', table);

        const usersMobileConfig = {
        entity: 'user',
        primary: [{ key: 'code', label: 'Code' }, { key: 'nom', label: 'Nom' }],
        secondary: [{ key: 'prenom', label: 'Prénom' }, { key: 'telephone', label: 'Téléphone' }, { key: 'role', label: 'Rôle' }],
        detailUrl: function(r) { return LINK + 'user/details/' + r.editId; },
        actions: [
            { id: 'voir', label: 'Voir', icon: 'eye', href: function(r) { return LINK + 'user/details/' + r.editId; } },
            { id: 'modifier', label: 'Modifier', icon: 'edit', href: function(r) { return LINK + 'user/edition/' + r.editId; } },
            { id: 'role', label: 'Rôle', icon: 'shield', onClick: function(rowData) {
                const role = prompt('Rôle de ' + rowData.nom + ' (' + rowData.code + ')\nROLE-ADMIN | ROLE-PRO | ROLE-LIV', rowData.role_code || 'ROLE-PRO');
                if (role === null) return;
                $.post(LINK + 'user/setRole', { id_user: rowData.id, role_code: role }, function(rep) {
                    showToast(rep.message || 'Rôle mis à jour', rep.status ? 'success' : 'error');
                    if (rep.status) $('#dataTable').DataTable().ajax.reload();
                }, 'json').fail(function() { showToast('Erreur serveur', 'error'); });
            }},
        ],
        getActions: function(row) {
            var list = usersMobileConfig.actions.map(function(a) { return Object.assign({}, a, { href: a.href ? a.href(row) : undefined }); });
            var isActive = row.statut === 'actif';
            list.push({
                id: isActive ? 'desactiver' : 'activer',
                label: isActive ? 'Désactiver' : 'Activer',
                icon: isActive ? 'toggle-left' : 'toggle-right',
                onClick: function(rowData) {
                    showConfirm('Changer le statut de cet utilisateur ?', function() {
                        $.post(LINK + 'user/changer', { id: rowData.id }, function(rep) {
                            showToast(rep.message || 'Statut changé', rep.status ? 'success' : 'error');
                            $('#dataTable').DataTable().ajax.reload();
                        }, 'json').fail(function() { showToast('Erreur serveur', 'error'); });
                    });
                }
            });
            return list;
        }
    };
    renderMobileCards('dataTable', usersMobileConfig);
    }

    $('#telephone').on('input blur', function() {
        const val = $(this).val().trim();
        const errDiv = $('#telephoneError');
        const isEdit = $('#id_user').val() !== '';
        if (val.length === 10 && !isEdit) {
            $.post(LINK + 'user/checkPhone', { telephone: val }, function(rep) {
                if (!rep.status) {
                    errDiv.css({'color': '#EF4444', 'font-size': '12px', 'margin-top': '4px', 'font-weight': '600'}).html('<i class="fa fa-exclamation-triangle"></i> ' + rep.message);
                    $('.formEditUser button[type="submit"]').prop('disabled', true);
                } else {
                    errDiv.css({'color': '#10B981', 'font-size': '12px', 'margin-top': '4px', 'font-weight': '600'}).html('<i class="fa fa-check-circle"></i> Numéro disponible');
                    $('.formEditUser button[type="submit"]').prop('disabled', false);
                }
            }, 'json');
        } else {
            errDiv.html('');
            $('.formEditUser button[type="submit"]').prop('disabled', false);
        }
    });

    $('.formEditUser').on('submit', function(e) {
        e.preventDefault();
        $('#editUserAlert').hide().html('');
        const form = $(this);
        const btn = form.find('.btn_actions');
        loading(btn, true, '<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

        $.ajax({
            url: LINK + 'user/edit',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                loading(btn, false, '<i class="fa fa-save"></i> Sauvegarder');
                if (rep.status) {
                    showToast(rep.message, 'success');
                    setTimeout(() => window.location.href = LINK + 'user/list', 700);
                } else {
                    $('#editUserAlert').css('display', 'flex').html('<i class="fa fa-exclamation-circle"></i> ' + rep.message);
                    showToast(rep.message, 'error');
                }
            },
            error: function(xhr) {
                loading(btn, false, '<i class="fa fa-save"></i> Sauvegarder');
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erreur lors de l\'enregistrement';
                $('#editUserAlert').css('display', 'flex').html('<i class="fa fa-exclamation-circle"></i> ' + msg);
                showToast(msg, 'error');
            }
        });
    });
});
