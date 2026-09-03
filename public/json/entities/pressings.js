$(document).ready(function() {
    if ($('#dataTable').length) {
        const columns = [
            { title: 'N°', data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
            { data: 'code', title: 'Code' },
            { data: 'libelle', title: 'Libellé' },
            { data: 'telephone', title: 'Téléphone' },
            { data: 'email', title: 'Email' },
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
                            <a href="${LINK}pressing/details/${row.editId}" title="Voir" class="btn-action btn-action-secondary"><i class="fa fa-eye"></i></a>
                            <a href="${LINK}pressing/edition/${row.editId}" title="Modifier" class="btn-action btn-action-primary"><i class="fa fa-edit"></i></a>
                        </div>
                    `;
                }
            }
        ];

        const table = initDataTable('dataTable', 'pressing/apiList', columns);
        bindStatusToggle('dataTable', 'pressing/changer', table);

        const pressingsMobileConfig = {
        entity: 'pressing',
        primary: [{ key: 'libelle', label: 'Pressing' }],
        secondary: [{ key: 'code', label: 'Code' }, { key: 'telephone', label: 'Téléphone' }],
        detailUrl: function(r) { return LINK + 'pressing/details/' + r.editId; },
        actions: [
            { id: 'voir', label: 'Voir', icon: 'eye', href: function(r) { return LINK + 'pressing/details/' + r.editId; } },
            { id: 'modifier', label: 'Modifier', icon: 'edit', href: function(r) { return LINK + 'pressing/edition/' + r.editId; } },
        ],
        getActions: function(row) {
            var list = pressingsMobileConfig.actions.map(function(a) { return Object.assign({}, a, { href: a.href(row) }); });
            var isActive = row.statut === 'actif';
            list.push({
                id: isActive ? 'desactiver' : 'activer',
                label: isActive ? 'Désactiver' : 'Activer',
                icon: isActive ? 'toggle-left' : 'toggle-right',
                onClick: function(rowData) {
                    showConfirm('Changer le statut de ce pressing ?', function() {
                        $.post(LINK + 'pressing/changer', { id: rowData.id }, function(rep) {
                            showToast(rep.message || 'Statut changé', rep.status ? 'success' : 'error');
                            $('#dataTable').DataTable().ajax.reload();
                        }, 'json').fail(function() { showToast('Erreur serveur', 'error'); });
                    });
                }
            });
            return list;
        }
    };
    renderMobileCards('dataTable', pressingsMobileConfig);
    }

    $('.formEditPressing').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');
        const idPressing = form.find('#id_pressing').val();
        const isAdd = !idPressing;
        const url = isAdd ? LINK + 'pressing/add' : LINK + 'pressing/edit';

        if (isAdd) {
            // Validation personnalisée des étapes
            const libelle = $.trim($('#libelle_pressing').val());
            const nomUser = $.trim($('#nom_user').val());
            const emailUser = $.trim($('#email_user').val());
            const passwordUser = $.trim($('#password_user').val());
            const forfaitCode = $('input[name="forfait_code"]:checked').val();

            if (!libelle) {
                showToast('Veuillez saisir le nom du pressing !', 'warning');
                if (typeof goToStep === 'function') goToStep(1);
                $('#libelle_pressing').focus();
                return;
            }
            if (!nomUser) {
                showToast('Veuillez saisir le nom du gérant responsable !', 'warning');
                if (typeof goToStep === 'function') goToStep(2);
                $('#nom_user').focus();
                return;
            }
            if (!emailUser) {
                showToast('Veuillez saisir l\'email de connexion du gérant !', 'warning');
                if (typeof goToStep === 'function') goToStep(2);
                $('#email_user').focus();
                return;
            }
            if (!passwordUser) {
                showToast('Veuillez saisir un mot de passe de connexion !', 'warning');
                if (typeof goToStep === 'function') goToStep(2);
                $('#password_user').focus();
                return;
            }
            if (!forfaitCode) {
                showToast('Veuillez sélectionner un forfait B2B !', 'warning');
                if (typeof goToStep === 'function') goToStep(3);
                return;
            }
        }

        loading(btn, true, '<i class="fa fa-spinner fa-spin"></i> Enregistrement en cours...');

        const formData = new FormData(this);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(rep) {
                loading(btn, false, '<i class="fa fa-check-circle"></i> Valider & Activer le Pressing (Tout-en-Un)');
                if (rep.status) {
                    showToast(rep.message || 'Pressing enregistré avec succès !', 'success');
                    setTimeout(() => window.location.href = LINK + 'pressing/list', 800);
                } else {
                    showToast(rep.message || 'Erreur lors de l\'enregistrement', 'error');
                }
            },
            error: function(xhr) {
                loading(btn, false, '<i class="fa fa-save"></i> Enregistrer');
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erreur lors de la communication avec le serveur';
                showToast(msg, 'error');
            }
        });
    });
});
