$(document).ready(function() {
    if ($('#dataTable').length) {
        const isSuperAdmin = $('#dataTable').attr('data-superadmin') === '1';

        let columns = [];
        columns.push({ title: 'N°', data: null, render: function(data, type, row, meta) { return meta.row + 1; } });

        if (isSuperAdmin) {
            columns.push({ data: 'pressing_name', title: 'Pressing' });
        }

        columns.push(
            { data: 'jour', title: 'Jour', render: function(d) { return '<strong style="font-size: 14px; color: #1E293B;">' + (d || '') + '</strong>'; } },
            { data: 'heure_ouverture', title: 'Ouverture' },
            { data: 'heure_fermeture', title: 'Fermeture' },
            {
                data: 'statut',
                title: 'Statut',
                render: function(data, type, row) {
                    const isOuvert = (data === 'Ouvert' || row.est_ferme == 0);
                    if (isOuvert) {
                        return '<span style="background-color: #ECFDF5 !important; color: #059669 !important; border: 1px solid #A7F3D0 !important; border-radius: 6px; padding: 4px 10px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;"><i class="fa fa-check-circle"></i> Ouvert</span>';
                    } else {
                        return '<span style="background-color: #FEF2F2 !important; color: #DC2626 !important; border: 1px solid #FECACA !important; border-radius: 6px; padding: 4px 10px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;"><i class="fa fa-times-circle"></i> Fermé</span>';
                    }
                }
            },
            {
                data: null,
                title: 'Actions',
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <div class="table-actions">
                            <a href="${LINK}horaire/edition/${row.editId}" title="Modifier" class="btn-action btn-action-primary"><i class="fa fa-edit"></i></a>
                        </div>
                    `;
                }
            }
        );

        const table = initDataTable('dataTable', 'horaire/apiList', columns);

        const horairesMobileConfig = {
            entity: 'horaire',
            primary: isSuperAdmin ? [{ key: 'pressing_name', label: 'Pressing' }, { key: 'jour', label: 'Jour' }] : [{ key: 'jour', label: 'Jour' }],
            secondary: [{ key: 'statut', label: 'Statut' }],
            detailUrl: function(r) { return LINK + 'horaire/edition/' + r.editId; },
            actions: [
                { id: 'modifier', label: 'Modifier', icon: 'edit', href: function(r) { return LINK + 'horaire/edition/' + r.editId; } },
            ],
            getActions: function(row) {
                var list = horairesMobileConfig.actions.map(function(a) { return Object.assign({}, a, { href: a.href(row) }); });
                return list;
            }
        };
        renderMobileCards('dataTable', horairesMobileConfig);
    }

    $('.formEditHoraire').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');
        const idHoraire = form.find('#id_horaire').val();
        const isAdd = !idHoraire;
        const url = isAdd ? LINK + 'horaire/add' : LINK + 'horaire/edit';

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
                    setTimeout(() => window.location.href = LINK + 'horaire/list', 700);
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
