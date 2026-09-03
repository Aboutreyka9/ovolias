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
                title: 'Libellé Forfait & Avantages Inclus',
                render: function(data, type, row) {
                    let html = '<div><strong style="color: #1E293B; font-size: 14px;">' + (data || '') + '</strong>';
                    if (row.description) {
                      html += '<div style="font-size: 12px; color: #64748B; margin-top: 2px;">' + row.description + '</div>';
                    }
                    if (row.avantages && row.avantages.length > 0) {
                      html += '<div style="margin-top: 8px; display: flex; flex-direction: column; gap: 3px;">';
                      row.avantages.forEach(adv => {
                        html += '<div style="font-size: 11px; font-weight: 600; color: #047857; display: inline-flex; align-items: center; gap: 5px;"><i class="fa fa-check-circle" style="color: #10B981; font-size: 11px;"></i> <span>' + adv + '</span></div>';
                      });
                      html += '</div>';
                    }
                    html += '</div>';
                    return html;
                }
            },
            { 
                data: 'montant', 
                title: 'Montant',
                render: function(data) {
                    const val = Number(data || 0).toLocaleString('fr-FR');
                    return '<span style="font-weight: 800; color: #059669;">' + val + ' FCFA</span>';
                }
            },
            { 
                data: 'duree_mois', 
                title: 'Durée',
                render: function(data) {
                    const mois = parseInt(data || 1);
                    return '<span style="color: #64748B; font-weight: 600;">' + mois + ' mois</span>';
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
                           '<input type="checkbox" class="btnToggleForfaitStatut" data-id="' + row.id + '" ' + checkedAttr + ' style="opacity:0; width:0; height:0;">' +
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
                            <a href="${baseApi}forfait/edition/${row.editId}" class="btn-action btn-action-primary" title="Modifier le forfait">
                                <i class="fa fa-edit"></i>
                            </a>
                        </div>
                    `;
                }
            }
        ];

        const table = initDataTable('dataTable', 'forfait/apiList', columns);

        // Toggle Status Forfait
        $(document).on('change', '.btnToggleForfaitStatut', function() {
            const id = $(this).data('id');
            const isChecked = $(this).is(':checked');
            const $input = $(this);
            if (!id) return;

            $.post(baseApi + 'forfait/changer', { id: id }, function(rep) {
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
        const forfaitsMobileConfig = {
            entity: 'forfait',
            primary: [{ key: 'code', label: 'Code' }, { key: 'libelle', label: 'Libellé' }],
            secondary: [{ key: 'montant', label: 'Montant' }, { key: 'duree_mois', label: 'Durée (mois)' }],
            actions: [
                { id: 'modifier', label: 'Modifier', icon: 'edit', href: function(r) { return baseApi + 'forfait/edition/' + r.editId; } }
            ]
        };
        renderMobileCards('dataTable', forfaitsMobileConfig);
    }

    // Formulaire d'ajout de forfait
    $('#formAddForfait').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');

        if (typeof loading === 'function') loading(btn, true, 'Enregistrement...');

        $.ajax({
            url: baseApi + 'forfait/add',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                if (typeof loading === 'function') loading(btn, false);
                if (rep.status) {
                    if (typeof showToast === 'function') showToast(rep.message || 'Forfait créé avec succès !', 'success');
                    closeAddForfaitModal();
                    form[0].reset();
                    if ($('#dataTable').length) {
                        $('#dataTable').DataTable().ajax.reload(null, false);
                    }
                } else {
                    if (typeof showToast === 'function') showToast(rep.message || 'Erreur lors de la création', 'error');
                }
            },
            error: function(xhr) {
                if (typeof loading === 'function') loading(btn, false);
                let msg = 'Erreur lors de la création';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (typeof showToast === 'function') showToast(msg, 'error');
            }
        });
    });

    // Formulaire d'édition de forfait
    $('#formEditForfait').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');

        if (typeof loading === 'function') loading(btn, true, 'Sauvegarde...');

        $.ajax({
            url: baseApi + 'forfait/edit',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                if (typeof loading === 'function') loading(btn, false);
                if (rep.status) {
                    if (typeof showToast === 'function') showToast(rep.message || 'Forfait modifié avec succès !', 'success');
                    setTimeout(function() {
                        window.location.href = baseApi + 'forfait/list';
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

function openAddForfaitModal() {
    $('#modalAddForfait').css('display', 'flex');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeAddForfaitModal() {
    $('#modalAddForfait').hide();
}
