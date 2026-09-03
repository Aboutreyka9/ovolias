$(document).ready(function() {
    if ($('#dataTable').length) {
        const columns = [
            { title: 'N°', data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
            { data: 'code', title: 'Code' },
            { data: 'nom', title: 'Nom' },
            { data: 'prenom', title: 'Prénom' },
            { data: 'telephone', title: 'Téléphone' },
            { data: 'pressing', title: 'Pressing' },
            {
                data: 'statut',
                title: 'Statut',
                className: 'text-center',
                render: function(data, type, row) {
                    var isActif = (data === 'actif');
                    var checkedAttr = isActif ? 'checked' : '';
                    return '<div style="display:flex; justify-content:center; align-items:center;">' +
                           '<label style="position:relative; display:inline-block; width:38px; height:20px; margin:0; cursor:pointer;" title="' + (isActif ? 'Actif - Cliquez pour désactiver' : 'Inactif - Cliquez pour activer') + '">' +
                           '<input type="checkbox" class="changerStatus" data-id="' + row.id + '" ' + checkedAttr + ' style="opacity:0; width:0; height:0;">' +
                           '<span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:' + (isActif ? '#15803D' : '#CBD5E1') + '; transition:.3s; border-radius:20px;">' +
                           '<span style="position:absolute; content:\'\'; height:14px; width:14px; left:' + (isActif ? '20px' : '3px') + '; bottom:3px; background-color:white; transition:.3s; border-radius:50%;"></span>' +
                           '</span>' +
                           '</label>' +
                           '</div>';
                }
            },
            {
                data: null,
                title: 'Actions',
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <div class="table-actions">
                            <a href="${LINK}livreur/details/${row.editId}" title="Voir" class="btn-action btn-action-secondary"><i class="fa fa-eye"></i></a>
                            <a href="${LINK}livreur/edition/${row.editId}" title="Modifier" class="btn-action btn-action-primary"><i class="fa fa-edit"></i></a>
                        </div>
                    `;
                }
            }
        ];

        const table = initDataTable('dataTable', 'livreur/apiList', columns);
        bindStatusToggle('dataTable', 'livreur/changer', table);

        const livreursMobileConfig = {
        entity: 'livreur',
        primary: [{ key: 'nom', label: 'Livreur' }],
        secondary: [{ key: 'code', label: 'Code' }, { key: 'telephone', label: 'Téléphone' }],
        detailUrl: function(r) { return LINK + 'livreur/details/' + r.editId; },
        actions: [
            { id: 'voir', label: 'Voir', icon: 'eye', href: function(r) { return LINK + 'livreur/details/' + r.editId; } },
            { id: 'modifier', label: 'Modifier', icon: 'edit', href: function(r) { return LINK + 'livreur/edition/' + r.editId; } },
        ],
        getActions: function(row) {
            var list = livreursMobileConfig.actions.map(function(a) { return Object.assign({}, a, { href: a.href(row) }); });
            var isActive = row.statut === 'actif';
            list.push({
                id: isActive ? 'desactiver' : 'activer',
                label: isActive ? 'Désactiver' : 'Activer',
                icon: isActive ? 'toggle-left' : 'toggle-right',
                onClick: function(rowData) {
                    showConfirm('Changer le statut de ce livreur ?', function() {
                        $.post(LINK + 'livreur/changer', { id: rowData.id }, function(rep) {
                            showToast(rep.message || 'Statut changé', rep.status ? 'success' : 'error');
                            $('#dataTable').DataTable().ajax.reload();
                        }, 'json').fail(function() { showToast('Erreur serveur', 'error'); });
                    });
                }
            });
            return list;
        }
    };
    renderMobileCards('dataTable', livreursMobileConfig);
    }

    $('.formEditLivreur').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');
        const idLivreur = form.find('#id_livreur').val();
        const isAdd = !idLivreur;
        const url = isAdd ? LINK + 'livreur/add' : LINK + 'livreur/edit';

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
                    setTimeout(() => window.location.href = LINK + 'livreur/list', 700);
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
