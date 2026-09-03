$(document).ready(function() {
    if ($('#dataTable').length) {
        const columns = [
            { title: 'N°', data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
            { data: 'code', title: 'Code' },
            { data: 'libelle', title: 'Libellé' },
            { data: 'groupe', title: 'Groupe' },
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
                            <a href="${LINK}permission/edition/${row.editId}" title="Modifier" class="btn-action btn-action-primary"><i class="fa fa-edit"></i></a>
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

        const table = initDataTable('dataTable', 'permission/apiList', columns);
        bindStatusToggle('dataTable', 'permission/changer', table);

        const permissionsMobileConfig = {
            entity: 'permission',
            primary: [{ key: 'code', label: 'Code' }, { key: 'libelle', label: 'Libellé' }],
            secondary: [{ key: 'groupe', label: 'Groupe' }],
            actions: [
                { id: 'modifier', label: 'Modifier', icon: 'edit', href: function(r) { return LINK + 'permission/edition/' + r.editId; } },
            ],
            getActions: function(row) {
                var list = permissionsMobileConfig.actions.map(function(a) { return Object.assign({}, a, { href: a.href(row) }); });
                var isActive = row.statut === 'actif';
                list.push({
                    id: isActive ? 'desactiver' : 'activer',
                    label: isActive ? 'Désactiver' : 'Activer',
                    icon: isActive ? 'toggle-left' : 'toggle-right',
                    onClick: function(rowData) {
                        showConfirm('Changer le statut de cette permission ?', function() {
                            $.post(LINK + 'permission/changer', { id: rowData.id }, function(rep) {
                                showToast(rep.message || 'Statut changé', rep.status ? 'success' : 'error');
                                $('#dataTable').DataTable().ajax.reload();
                            }, 'json').fail(function() { showToast('Erreur serveur', 'error'); });
                        });
                    }
                });
                return list;
            }
        };
        renderMobileCards('dataTable', permissionsMobileConfig);
    }

    $('.formEditPermission').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');
        const isAdd = !$('#id_permission').val();
        const url = isAdd ? LINK + 'permission/add' : LINK + 'permission/edit';

        loading(btn, true, '<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                loading(btn, false, '<i class="fa fa-save"></i> Sauvegarder');
                if (rep.status) {
                    showToast(rep.message, 'success');
                    setTimeout(() => window.location.href = LINK + 'permission/list', 700);
                } else {
                    showToast(rep.message, 'error');
                }
            },
            error: function() {
                loading(btn, false, '<i class="fa fa-save"></i> Sauvegarder');
                showToast('Erreur serveur', 'error');
            }
        });
    });
});
