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
                data: 'code', 
                title: 'Code',
                render: function(data) {
                    return '<span class="code-badge">' + (data || '') + '</span>';
                }
            },
            { 
                data: 'libelle', 
                title: 'Libellé Ville',
                render: function(data) {
                    return '<strong style="color: #1E293B;">' + (data || '') + '</strong>';
                }
            },
            { 
                data: 'total_quartiers', 
                title: 'Quartiers Rattachés',
                render: function(data, type, row) {
                    const count = parseInt(data || 0);
                    const badgeColor = count > 0 ? 'background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE;' : 'background: #F1F5F9; color: #64748B; border: 1px solid #E2E8F0;';
                    return `
                        <a href="${baseApi}ville/edition/${row.editId}" style="text-decoration: none;">
                            <span style="font-weight: 700; font-size: 12px; padding: 4px 10px; border-radius: 999px; display: inline-flex; align-items: center; gap: 5px; ${badgeColor}">
                                <i class="fa fa-map-marker-alt"></i> ${count} quartier${count > 1 ? 's' : ''}
                            </span>
                        </a>
                    `;
                }
            },
            { 
                data: 'statut', 
                title: 'Statut',
                className: 'text-center',
                render: function(data, type, row) {
                    var isActif = (data === 'actif');
                    var checkedAttr = isActif ? 'checked' : '';
                    return '<div style="display:flex; justify-content:center; align-items:center;">' +
                           '<label style="position:relative; display:inline-block; width:38px; height:20px; margin:0; cursor:pointer;" title="' + (isActif ? 'Actif - Cliquez pour désactiver' : 'Inactif - Cliquez pour activer') + '">' +
                           '<input type="checkbox" class="btnToggleVilleStatut" data-id="' + row.id + '" ' + checkedAttr + ' style="opacity:0; width:0; height:0;">' +
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
                            <a href="${baseApi}ville/edition/${row.editId}" class="btn-action btn-action-primary" title="Gérer la ville et ses quartiers">
                                <i class="fa fa-edit"></i>
                            </a>
                        </div>
                    `;
                }
            }
        ];

        const table = initDataTable('dataTable', 'ville/apiList', columns);

        // Toggle Status Ville
        $(document).on('change', '.btnToggleVilleStatut', function() {
            const id = $(this).data('id');
            const isChecked = $(this).is(':checked');
            const $input = $(this);
            if (!id) return;

            $.post(baseApi + 'ville/changer', { id: id }, function(rep) {
                if (rep.status) {
                    if (typeof showToast === 'function') showToast(rep.message || 'Statut mis à jour', 'success');
                    table.ajax.reload(null, false);
                } else {
                    if (typeof showToast === 'function') showToast(rep.message || 'Erreur', 'error');
                    $input.prop('checked', !isChecked);
                }
            }, 'json').fail(function() {
                if (typeof showToast === 'function') showToast('Erreur serveur', 'error');
                $input.prop('checked', !isChecked);
            });
        });

        // Mobile cards
        const villesMobileConfig = {
            entity: 'ville',
            primary: [{ key: 'code', label: 'Code' }, { key: 'libelle', label: 'Libellé' }],
            secondary: [{ key: 'total_quartiers', label: 'Quartiers' }, { key: 'statut', label: 'Statut' }],
            actions: [
                { id: 'modifier', label: 'Gérer la ville & quartiers', icon: 'edit', href: function(r) { return baseApi + 'ville/edition/' + r.editId; } }
            ]
        };
        renderMobileCards('dataTable', villesMobileConfig);
    }

    // Formulaire d'ajout de ville
    $('#formAddVille').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');

        if (typeof loading === 'function') loading(btn, true, 'Enregistrement...');

        $.ajax({
            url: baseApi + 'ville/add',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                if (typeof loading === 'function') loading(btn, false);
                if (rep.status) {
                    if (typeof showToast === 'function') showToast(rep.message || 'Ville ajoutée avec succès !', 'success');
                    closeAddVilleModal();
                    form[0].reset();
                    if ($('#dataTable').length) {
                        $('#dataTable').DataTable().ajax.reload(null, false);
                    }
                } else {
                    if (typeof showToast === 'function') showToast(rep.message || 'Erreur lors de l\'ajout', 'error');
                }
            },
            error: function(xhr) {
                if (typeof loading === 'function') loading(btn, false);
                let msg = 'Erreur lors de l\'ajout';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (typeof showToast === 'function') showToast(msg, 'error');
            }
        });
    });

    // Formulaire d'édition de ville
    $('#formEditVille').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');

        if (typeof loading === 'function') loading(btn, true, 'Sauvegarde...');

        $.ajax({
            url: baseApi + 'ville/edit',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                if (typeof loading === 'function') loading(btn, false);
                if (rep.status) {
                    if (typeof showToast === 'function') showToast(rep.message || 'Ville modifiée avec succès !', 'success');
                    setTimeout(function() {
                        window.location.href = baseApi + 'ville/list';
                    }, 800);
                } else {
                    if (typeof showToast === 'function') showToast(rep.message || 'Erreur lors de la modification', 'error');
                }
            },
            error: function(xhr) {
                if (typeof loading === 'function') loading(btn, false);
                let msg = 'Erreur serveur';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (typeof showToast === 'function') showToast(msg, 'error');
            }
        });
    });
});

function openAddVilleModal() {
    $('#modalAddVille').css('display', 'flex');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeAddVilleModal() {
    $('#modalAddVille').hide();
}
