const LINK = window.location.origin + '/ovolias/';

$.ajaxSetup({
    xhrFields: {
        withCredentials: true
    }
});

// Empêcher la molette de la souris de modifier la valeur des champs input type="number"
document.addEventListener('wheel', function(e) {
    if (document.activeElement && document.activeElement.type === 'number') {
        document.activeElement.blur();
    }
}, { passive: true });

const dataTableDefaults = {
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
    language: {
        emptyTable: "Aucune donn\u00e9e disponible dans le tableau",
        loadingRecords: "Chargement...",
        processing: "Traitement...",
        search: "Rechercher\u00a0:",
        lengthMenu: "Afficher _MENU_ entr\u00e9es",
        zeroRecords: "Aucune entr\u00e9e correspondante trouv\u00e9e",
        info: "Affichage de _START_ \u00e0 _END_ sur _TOTAL_ entr\u00e9es",
        infoEmpty: "Affichage de 0 \u00e0 0 sur 0 entr\u00e9es",
        infoFiltered: "(filtr\u00e9es depuis un total de _MAX_ entr\u00e9es)",
        paginate: { first: "Premi\u00e8re", last: "Derni\u00e8re", next: "Suivante", previous: "Pr\u00e9c\u00e9dente" },
        aria: { sortAscending: " : activer pour trier la colonne par ordre croissant", sortDescending: " : activer pour trier la colonne par ordre d\u00e9croissant" },
        thousands: " ",
        decimal: ","
    },
    processing: true,
    serverSide: false
};

function initDataTable(tableId, ajaxUrl, columns) {
    return $(`#${tableId}`).DataTable({
        ...dataTableDefaults,
        ajax: { url: LINK + ajaxUrl, dataSrc: 'data' },
        columns: columns,
        drawCallback: function() {
            if (window.innerWidth <= 768) {
                const api = $(`#${tableId}`).DataTable();
                const headers = [];
                $(`#${tableId} thead th`).each(function() {
                    headers.push($(this).text().trim());
                });
                $(api.rows().nodes()).each(function() {
                    $(this).find('td').each(function(idx, td) {
                        if (headers[idx]) $(td).attr('data-label', headers[idx]);
                    });
                });
            }
        }
    });
}

function bindStatusToggle(tableId, changerUrl, tableRef) {
    $(`#${tableId}`).on('click', '.changerStatus', function() {
        const id = $(this).data('id');
        $.post(LINK + changerUrl, { id: id }, function(response) {
            console.log(response);
            
            showToast(response.message, response.status === 1 ? 'success' : 'error');
            if (response.status === 1 && response.reload) {
                $(`#${tableId}`).DataTable().ajax.reload(null, false);
            }
        }, 'json');
    });
}

const entityConfigs = {
    product: {
        modalTitle: 'Ajouter un produit',
        fields: [
            { name: 'libelle', label: 'Libellé', type: 'text', required: true, col: 1 },
            { name: 'categorie_code', label: 'Catégorie', type: 'select', required: true, optionsKey: 'PRODUCT_CATEGORIE_OPTIONS', col: 2 },
            { name: 'sous_categorie_code', label: 'Sous-catégorie', type: 'select', required: false, col: 1 },
            { name: 'fournisseur_code', label: 'Fournisseur', type: 'select', required: true, optionsKey: 'PRODUCT_FOURNISSEUR_OPTIONS', col: 2 },
            { name: 'prix_fournisseur', label: 'Prix fournisseur (FCFA)', type: 'number', required: false, col: 1 },
            { name: 'prix_vente', label: 'Prix vente (FCFA)', type: 'number', required: true, col: 2 },
            { name: 'stock', label: 'Stock initial', type: 'number', required: false, col: 1 },
            { name: 'description', label: 'Description', type: 'textarea', required: false, col: 'full' },
            { name: 'images', label: 'Images du produit', type: 'image-upload', required: false, col: 'full' }
        ]
    },
    category: {
        modalTitle: 'Ajouter une catégorie',
        fields: [
            { name: 'libelle', label: 'Libellé', type: 'text', placeholder: 'Ex: Vêtements homme, Linge de maison...', required: true },
            { name: 'description', label: 'Description', type: 'textarea', placeholder: 'Description de la catégorie...', required: false }
        ]
    },
    client: {
        modalTitle: 'Ajouter un client',
        fields: [
            { name: 'nom', label: 'Nom & Prénoms', type: 'text', placeholder: 'Ex: Kouassi Jean', required: true },
            { name: 'telephone', label: 'Téléphone', type: 'tel', placeholder: 'Ex: 0708091011', required: true },
            { name: 'email_client', label: 'Email', type: 'email', placeholder: 'Ex: client@lavex.ci (Optionnel)', required: false },
            { name: 'quartier_client', label: 'Quartier', type: 'text', placeholder: 'Ex: Angré 8ème Tranche', required: false },
            { name: 'adresse_client', label: 'Adresse', type: 'textarea', placeholder: 'Ex: Rue des Jardins, Villa 14', required: false }
        ]
    },
    utilisateur: {
        modalTitle: 'Ajouter un membre d\'équipe',
        fields: [
            { name: 'nom', label: 'Nom', type: 'text', placeholder: 'Ex: Kouassi', required: true, col: 1 },
            { name: 'prenom', label: 'Prénom', type: 'text', placeholder: 'Ex: Jean-Marc', required: false, col: 2 },
            { name: 'telephone', label: 'Téléphone (Login de connexion)', type: 'tel', placeholder: 'Ex: 0708091011', required: true, col: 1 },
            { name: 'email', label: 'Email (Optionnel)', type: 'email', placeholder: 'Ex: agent@lavex.ci', required: false, col: 2 },
            { name: 'role_code', label: 'Rôle attribué', type: 'select', required: true, options: { 'ROLE-PRO': 'Propriétaire', 'ROLE-GEST': 'Gestionnaire', 'ROLE-LIV': 'Livreur' }, col: 1 },
            { name: 'password', label: 'Mot de passe par défaut', type: 'text', value: '12345', readonly: true, required: false, col: 2 }
        ]
    },
    order: {
        modalTitle: 'Nouvelle commande',
        fields: [
            { name: 'client_code', label: 'Code client', type: 'text', placeholder: 'Ex: CLI-123456', required: true },
            { name: 'adresse_livraison_commande', label: 'Adresse', type: 'textarea', placeholder: 'Adresse précise de livraison...', required: true },
            { name: 'montant_total_commande', label: 'Montant', type: 'number', placeholder: '0', required: true },
            { name: 'statut_commande', label: 'Statut', type: 'select', required: false, options: { 'en_attente': 'En attente', 'confirmee': 'Confirmée', 'en_livraison': 'En livraison', 'livree': 'Livrée', 'annulee': 'Annulée' } },
            { name: 'methode_paiement_commande', label: 'Paiement', type: 'select', required: false, options: { 'cash': 'Espèces', 'mobile_money': 'Mobile Money', 'carte': 'Carte' } }
        ]
    },
    fournisseur: {
        modalTitle: 'Ajouter un fournisseur',
        fields: [
            { name: 'nom', label: 'Nom', type: 'text', placeholder: 'Ex: Grossiste Produits Linge', required: true },
            { name: 'telephone', label: 'Téléphone', type: 'text', placeholder: 'Ex: 0708091011', required: true },
            { name: 'whatsapp', label: 'WhatsApp', type: 'text', placeholder: 'Ex: 0708091011', required: false },
            { name: 'localisation', label: 'Localisation', type: 'text', placeholder: 'Ex: Treichville Zone 3', required: false },
            { name: 'mode_collaboration', label: 'Mode collaboration', type: 'select', required: true, options: { 'achat_direct': 'Achat direct', 'dropshipping': 'Dropshipping', 'depot_vente': 'Dépôt vente', 'commission': 'Commission' } }
        ]
    },
    livreur: {
        modalTitle: 'Ajouter un livreur',
        fields: [
            { name: 'nom', label: 'Nom complet', type: 'text', placeholder: 'Ex: Yao Paul', required: true },
            { name: 'telephone', label: 'Téléphone', type: 'text', placeholder: 'Ex: 0708091011', required: true },
            { name: 'email', label: 'Email', type: 'email', placeholder: 'Ex: livreur@lavex.ci', required: false },
            { name: 'moyen_transport', label: 'Moyen de transport', type: 'select', required: true, options: { 'moto': 'Moto', 'voiture': 'Voiture', 'velo': 'Vélo', 'tricycle': 'Tricycle' } }
        ]
    },
    paiement: {
        modalTitle: 'Ajouter un paiement',
        fields: [
            { name: 'commande_code', label: 'Code commande', type: 'text', placeholder: 'Ex: CMD-123456', required: true },
            { name: 'montant_paiement', label: 'Montant (FCFA)', type: 'number', placeholder: 'Ex: 5000', required: true },
            { name: 'methode_paiement', label: 'Méthode', type: 'select', required: false, options: { 'cash': 'Espèces', 'mobile_money': 'Mobile Money', 'carte': 'Carte' } },
            { name: 'reference_transaction', label: 'Référence', type: 'text', placeholder: 'Ex: TXN-99887766', required: false },
            { name: 'statut_paiement', label: 'Statut', type: 'select', required: false, options: { 'en_attente': 'En attente', 'partiel': 'Partiel', 'paye': 'Payé', 'echoue': 'Échoué' } }
        ]
    },
    role: {
        modalTitle: 'Ajouter un rôle',
        fields: [
            { name: 'nom', label: 'Nom du rôle', type: 'text', placeholder: 'Ex: Gestionnaire Atelier', required: true },
            { name: 'description', label: 'Description', type: 'textarea', placeholder: 'Description des privilèges attribués...', required: false }
        ]
    },
    permission: {
        modalTitle: 'Ajouter une permission',
        fields: [
            { name: 'module', label: 'Module', type: 'text', placeholder: 'Ex: Commandes, Clients...', required: true },
            { name: 'action', label: 'Action', type: 'text', placeholder: 'Ex: Créer, Modifier, Supprimer...', required: true },
            { name: 'description', label: 'Description', type: 'textarea', placeholder: 'Détail de la permission...', required: false }
        ]
    },
    stock: {
        modalTitle: 'Nouveau mouvement',
        fields: [
            { name: 'produit_code', label: 'Code produit', type: 'text', required: true },
            { name: 'type_mouvement', label: 'Type', type: 'select', required: true, options: { 'entree': 'Entrée', 'sortie': 'Sortie' } },
            { name: 'quantite', label: 'Quantité', type: 'number', required: true },
            { name: 'source', label: 'Source', type: 'text', required: false }
        ]
    },
    commission: {
        modalTitle: 'Ajouter une commission',
        fields: [
            { name: 'commande_code', label: 'Code commande', type: 'text', required: true },
            { name: 'fournisseur_code', label: 'Code fournisseur', type: 'text', required: true },
            { name: 'montant_commission', label: 'Montant (FCFA)', type: 'number', required: true },
            { name: 'statut_commission', label: 'Statut', type: 'select', required: false, options: { 'en_attente': 'En attente', 'payee': 'Payée', 'annulee': 'Annulée' } }
        ]
    },
    setting: {
        modalTitle: 'Ajouter un paramètre',
        fields: [
            { name: 'libelle', label: 'Libellé', type: 'text', required: true },
            { name: 'valeur', label: 'Valeur', type: 'text', required: true },
            { name: 'type_parametre', label: 'Type', type: 'select', required: false, options: { 'texte': 'Texte', 'nombre': 'Nombre', 'bool': 'Booléen' } },
            { name: 'statut_parametre', label: 'Statut', type: 'select', required: false, options: { 'actif': 'Actif', 'inactif': 'Inactif' } }
        ]
    },
    city: {
        modalTitle: 'Ajouter une ville',
        fields: [
            { name: 'code', label: 'Code', type: 'text', required: true },
            { name: 'libelle', label: 'Libellé', type: 'text', required: true },
            { name: 'pays_code', label: 'Pays', type: 'text', required: false }
        ]
    },
    subcategory: {
        modalTitle: 'Ajouter une sous-catégorie',
        fields: [
            { name: 'libelle', label: 'Libellé', type: 'text', required: true },
            { name: 'categorie_code', label: 'Catégorie', type: 'select', required: true, optionsKey: 'SUBCATEGORY_CATEGORY_OPTIONS' },
            { name: 'description', label: 'Description', type: 'textarea', required: false }
        ]
    }
};

const entityOptionEndpoints = {
    SUBCATEGORY_CATEGORY_OPTIONS: LINK + 'category/getActive',
    PRODUCT_CATEGORIE_OPTIONS: LINK + 'category/getActive',
    PRODUCT_SOUSCATEGORIE_OPTIONS: LINK + 'subcategory/getActive',
    PRODUCT_FOURNISSEUR_OPTIONS: LINK + 'fournisseur/getActive'
};

function getEntityConfig(type) {
    let config = entityConfigs[type] || { modalTitle: 'Ajouter', fields: [] };
    let resolved = JSON.parse(JSON.stringify(config));
    resolved.type = type;
    resolved.fields.forEach(function(f) {
        if (f.optionsKey) {
        const opts = window[f.optionsKey];
            if (opts && typeof opts === 'object' && Object.keys(opts).length > 0) {
                f.options = opts;
            } else if (entityOptionEndpoints[f.optionsKey]) {
                f._optionsEndpoint = entityOptionEndpoints[f.optionsKey];
                f._optionsKey = f.optionsKey;
                f.options = { '': 'Chargement...' };
            } else {
                f.options = {};
            }
            delete f.optionsKey;
        }
    });
    return resolved;
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('genericModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalForm = document.getElementById('modalForm');
    const modalSave = document.getElementById('modalSave');
    let currentFormConfig = null;
    let currentSaveHandler = null;
    window.openModal = function(type, saveHandler) {
        // 1. Si c'est un ID d'élément DOM existant dans la page (qui n'est pas le genericModal)
        if (typeof type === 'string' && type) {
            const customEl = document.getElementById(type);
            if (customEl && customEl !== modal) {
                customEl.style.display = 'flex';
                customEl.classList.add('active');
                if (typeof lucide !== 'undefined') lucide.createIcons();
                return;
            }
        }

        if (!modal) return;
        currentFormConfig = getEntityConfig(type);
        if (!currentFormConfig) {
            console.warn('[openModal] Aucune configuration trouvée pour le type :', type);
            return;
        }
        currentFormType = type;
        const formType = type;
        currentSaveHandler = saveHandler || function(formData) {
            const endpoints = {
                product: LINK + 'article/add',
                category: LINK + 'categorie/add',
                client: LINK + 'client/add',
                utilisateur: LINK + 'user/add',
                order: LINK + 'commande/add',
                livreur: LINK + 'livreur/add',
                paiement: LINK + 'paiement/add',
                role: LINK + 'role/add',
                permission: LINK + 'permission/add',
                service: LINK + 'service/add',
                forfait: LINK + 'forfait/add',
                ville: LINK + 'ville/add',
                quartier: LINK + 'quartier/add',
                horaire: LINK + 'horaire/add',
                horaires: LINK + 'horaires/add'
            };
            const url = endpoints[type];

            if (!url) {
                showToast('Type de formulaire inconnu: ' + type, 'error');
                return;
            }

            loading(modalSave, true, '<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    loading(modalSave, false, 'Enregistrer');
                    if (response.status) {
                        showToast(response.message, 'success');
                        closeModal();
                        const table = $('#dataTable').DataTable();
                        if (table) table.ajax.reload(null, false);
                        console.log('[form success] type=', formType, 'response=', response);
                        if (formType === 'client' && response.client_code) {
                            setTimeout(function() {
                                window.location.href = LINK + 'client/list';
                            }, 400);
                        }
                    } else {
                        const errMsg = response.message || 'Erreur lors de l\'enregistrement';
                        $('#genericModalAlert').css('display', 'flex').html('<i class="fa fa-exclamation-circle"></i> ' + errMsg);
                        showToast(errMsg, 'error');
                    }
                },
                error: function(xhr) {
                    loading(modalSave, false, 'Enregistrer');
                    let errorMsg = 'Erreur serveur';
                    console.error('[form error] status:', xhr.status, 'response:', xhr.responseText);
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.status === 401) {
                        errorMsg = 'Authentification requise - Veuillez vous reconnecter';
                    } else if (xhr.status === 419) {
                        errorMsg = 'Token de sécurité invalide - Actualisez la page';
                    } else if (xhr.responseText) {
                        errorMsg = 'Erreur ' + xhr.status + ': ' + xhr.responseText.slice(0, 150);
                    }
                    $('#genericModalAlert').css('display', 'flex').html('<i class="fa fa-exclamation-circle"></i> ' + errorMsg);
                    showToast(errorMsg, 'error');
                }
            });
        };
        modalTitle.textContent = currentFormConfig.modalTitle;
        const isProductModal = type === 'product';
        const isUserModal = type === 'utilisateur';
        const isLargeModal = isProductModal || isUserModal;
        if (isLargeModal && window.innerWidth > 768) {
            modal.querySelector('.modal').style.maxWidth = isProductModal ? '900px' : '720px';
            modal.querySelector('.modal').style.width = '96%';
        } else {
            modal.querySelector('.modal').style.maxWidth = '550px';
            modal.querySelector('.modal').style.width = '96%';
        }
        let html = '<div id="genericModalAlert" style="display:none; padding: 12px 16px; border-radius: 10px; background: #FEF2F2; border: 1px solid #FCA5A5; color: #991B1B; font-weight: 700; font-size: 13px; margin-bottom: 16px; align-items: center; gap: 8px;"></div>';
        html += '<input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">';
        const fields = currentFormConfig.fields;
        const rows = [];
        const forceSingleCol = (type !== 'utilisateur') && fields.length <= 5;
        let row = [];
        fields.forEach(function(f) {
            f.options = f.options || {};
            if (forceSingleCol) {
                if (row.length) rows.push(row);
                rows.push([f]);
                row = [];
            } else if (f.col === 'full') {
                if (row.length) rows.push(row);
                rows.push([f]);
                row = [];
            } else if (f.col === 2 && row.length === 1) {
                row.push(f);
                rows.push(row);
                row = [];
            } else {
                if (row.length === 2) {
                    rows.push(row);
                    row = [];
                }
                row.push(f);
            }
        });
        if (row.length) rows.push(row);

        rows.forEach(function(row) {
            const isFull = row.length === 1;
            html += '<div class="' + (isFull ? 'form-row-full' : 'form-row') + '" style="display: flex; gap: 16px; width: 100%; margin-bottom: 14px; flex-wrap: wrap;">';
            row.forEach(function(f) {
                const isHalf = !isFull && row.length === 2;
                html += '<div class="form-group" style="flex: ' + (isHalf ? '1 1 calc(50% - 8px)' : '1 1 100%') + '; min-width: 220px; display: flex; flex-direction: column; gap: 6px;"><label style="font-weight: 700; font-size: 13px; color: #334155;">' + f.label + (f.required ? ' *' : '') + '</label><div class="input-wrapper" style="position: relative; width: 100%;">';
                if (f.type === 'select') {
                    html += '<select name="' + f.name + '" class="form-control" ' + (f.required ? 'required' : '') + ' style="border-radius: 8px; height: 42px; width: 100%; padding: 0 12px;">' + Object.entries(f.options || {}).map(function(e) { return '<option value="' + e[0] + '">' + e[1] + '</option>'; }).join('') + '</select>';
                } else if (f.type === 'textarea') {
                    html += '<textarea name="' + f.name + '" class="form-control" ' + (f.required ? 'required' : '') + ' style="border-radius: 8px; width: 100%; min-height: 80px;"></textarea>';
                } else if (f.type === 'image-upload') {
                    html += '<input type="file" name="image_files" multiple accept="image/*" class="image-upload-input" style="display:none">';
                    html += '<button type="button" class="btn btn-sm btn-outline-primary image-upload-trigger"><i class="fa fa-cloud-upload"></i> Ajouter des images</button>';
                    html += '<div class="image-previews-grid"></div>';
                } else {
                    const valAttr = f.value ? 'value="' + f.value + '"' : '';
                    const readAttr = f.readonly ? 'readonly style="background-color: #F1F5F9; color: #475569; font-weight: 700; cursor: not-allowed; border-radius: 8px; height: 42px; width: 100%; padding: 0 12px;"' : 'style="border-radius: 8px; height: 42px; width: 100%; padding: 0 12px;"';
                    const placeAttr = f.placeholder ? 'placeholder="' + f.placeholder + '"' : '';
                    html += '<input type="' + f.type + '" name="' + f.name + '" ' + valAttr + ' ' + placeAttr + ' class="form-control" ' + (f.required ? 'required' : '') + ' ' + readAttr + '>';
                }
                html += '<span class="field-feedback-icon" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;"></span></div></div>';
            });
            html += '</div>';
        });
        modalForm.innerHTML = html;
        modalForm.dataset.entity = type;
        modal.classList.add('active');
        modalForm._uploadedImages = [];

        if (isProductModal) {
            const fileInput = modalForm.querySelector('.image-upload-input');
            const triggerBtn = modalForm.querySelector('.image-upload-trigger');
            const previewsGrid = modalForm.querySelector('.image-previews-grid');

            triggerBtn.addEventListener('click', function() { fileInput.click(); });

            fileInput.addEventListener('change', function() {
                Array.from(this.files).forEach(function(file) {
                    uploadImageFile(file, previewsGrid, modalForm);
                });
                this.value = '';
            });

            function uploadImageFile(file, grid, form) {
                const fd = new FormData();
                fd.append('image', file);
                fd.append('csrf_token', form.querySelector('[name="csrf_token"]').value);
                $.ajax({
                    url: LINK + 'product/uploadImage',
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(resp) {
                        if (resp.status) {
                            const entry = { filename: resp.filename, url: LINK + resp.url, is_primary: false };
                            form._uploadedImages.push(entry);
                            renderPreview(entry, grid, form);
                        } else {
                            showToast(resp.message || 'Erreur upload', 'error');
                        }
                    },
                    error: function() { showToast('Erreur upload image', 'error'); }
                });
            }

            function renderPreview(entry, grid, form) {
                const card = document.createElement('div');
                card.className = 'image-preview-card';
                card.innerHTML = `
                    <img src="${entry.url}" alt="preview">
                    <div class="image-preview-actions">
                        <label class="principal-check">
                            <input type="radio" name="img_principal" value="${entry.filename}" ${entry.is_primary ? 'checked' : ''}> Principal
                        </label>
                        <button type="button" class="image-remove-btn" title="Supprimer">&times;</button>
                    </div>
                `;
                const radio = card.querySelector('input[type="radio"]');
                radio.addEventListener('change', function() {
                    form._uploadedImages.forEach(function(img) { img.is_primary = (img.filename === entry.filename); });
                });
                card.querySelector('.image-remove-btn').addEventListener('click', function() {
                    form._uploadedImages = form._uploadedImages.filter(function(img) { return img.filename !== entry.filename; });
                    card.remove();
                });
                grid.appendChild(card);
            }
        }

        currentFormConfig.fields.forEach(function(f) {
            if (f._optionsEndpoint) {
                const select = modalForm.querySelector('select[name="' + f.name + '"]');
                if (!select) return;
                $.get(f._optionsEndpoint, function(resp) {
                    const options = (resp && resp.options) ? resp.options : {};
                    const entries = Object.entries(options);
                    const placeholder = entries.find(function(e) { return e[0] === ''; });
                    const realOptions = entries.filter(function(e) { return e[0] !== ''; });
                    const placeholderHtml = placeholder
                        ? '<option value="" disabled selected>' + placeholder[1] + '</option>'
                        : '<option value="" disabled selected>Sélectionner...</option>';
                    select.innerHTML = placeholderHtml + realOptions.map(function(e) {
                        return '<option value="' + e[0] + '">' + e[1] + '</option>';
                    }).join('');
                }, 'json').fail(function(xhr) {
                    console.error('[openModal] Options load failed:', f._optionsEndpoint, 'status:', xhr.status, 'response:', xhr.responseJSON);
                    select.innerHTML = '<option value="">Erreur chargement</option>';
                });
            }
        });

        if (isProductModal) {
            const categorieSelect = modalForm.querySelector('select[name="categorie_code"]');
            const sousCategorieSelect = modalForm.querySelector('select[name="sous_categorie_code"]');
            console.log('[product modal] categorieSelect:', !!categorieSelect, 'sousCategorieSelect:', !!sousCategorieSelect);
            if (categorieSelect && sousCategorieSelect) {
                console.log('[product modal] attaching change listener');
                categorieSelect.addEventListener('change', function() {
                    console.log('[product modal] categorie changed to:', this.value);
                    const catCode = this.value;
                    if (!catCode) {
                        sousCategorieSelect.innerHTML = '<option value="" disabled selected>Sélectionner une sous-catégorie</option>';
                        sousCategorieSelect.disabled = true;
                        return;
                    }
                    sousCategorieSelect.disabled = true;
                    sousCategorieSelect.innerHTML = '<option value="">Chargement...</option>';
                    $.get(LINK + 'subcategory/getByCategory', { categorie_code: catCode }, function(resp) {
                        const options = resp.options || {};
                        const entries = Object.entries(options);
                        const placeholder = entries.find(function(e) { return e[0] === ''; });
                        const realOptions = entries.filter(function(e) { return e[0] !== ''; });
                        const placeholderHtml = placeholder
                            ? '<option value="" disabled selected>' + placeholder[1] + '</option>'
                            : '<option value="" disabled selected>Sélectionner...</option>';
                        sousCategorieSelect.innerHTML = placeholderHtml + realOptions.map(function(e) {
                            return '<option value="' + e[0] + '">' + e[1] + '</option>';
                        }).join('');
                        sousCategorieSelect.disabled = false;
                    }, 'json').fail(function() {
                        sousCategorieSelect.innerHTML = '<option value="">Erreur chargement</option>';
                        sousCategorieSelect.disabled = false;
                    });
                });
            }
        }
    };

    window.closeModal = function(id) {
        if (typeof id === 'string' && id) {
            const customEl = document.getElementById(id);
            if (customEl && customEl !== modal) {
                customEl.style.display = 'none';
                customEl.classList.remove('active');
                return;
            }
        }
        if (modal) {
            modal.classList.remove('active');
            modal.style.display = 'none';
            if (modalForm) modalForm.reset();
        }
        currentFormConfig = null;
        currentSaveHandler = null;
        currentFormType = null;
    };

    if (document.getElementById('modalClose')) {
        document.getElementById('modalClose').addEventListener('click', closeModal);
    }
    if (document.getElementById('modalCancel')) {
        document.getElementById('modalCancel').addEventListener('click', closeModal);
    }
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    }

if (modalSave) {
        modalSave.addEventListener('click', function() {
            if (!currentSaveHandler || !currentFormConfig) return;
            const form = document.getElementById('modalForm');
            const entityType = Object.keys(entityConfigs).find(key => entityConfigs[key] === currentFormConfig) || 'modal';
            const csrfTokenInput = form.querySelector('[name="csrf_token"]');
            const formData = {};
            
            currentFormConfig.fields.forEach(function(f) {
                const input = form.querySelector(`[name="${f.name}"]`);
                if (f.type === 'image-upload') {
                    formData[f.name] = JSON.stringify(form._uploadedImages || []);
                    return;
                }
                const value = input?.value || '';
                if (f.required || value.trim() !== '') {
                    formData[f.name] = value;
                }
            });
            
            if (csrfTokenInput) {
                formData.csrf_token = csrfTokenInput.value;
            }

            let isFormValid = true;
            if (typeof FormValidator !== 'undefined' && form.dataset.entity) {
                isFormValid = FormValidator.validateForm(form);
            } else {
                const requiredFields = currentFormConfig.fields.filter(f => f.required);
                requiredFields.forEach(function(f) {
                    const input = form.querySelector(`[name="${f.name}"]`);
                    if (input && !input.value.trim()) {
                        isFormValid = false;
                        input.style.borderColor = '#dc3545';
                        const wrapper = input.closest('.input-wrapper');
                        if (wrapper) {
                            let feedbackIcon = wrapper.querySelector('.field-feedback-icon');
                            if (feedbackIcon) {
                                feedbackIcon.classList.add('show');
                                feedbackIcon.innerHTML = FormValidator?.icons.error || '<i class="fa fa-exclamation-circle" style="color: #dc3545;"></i>';
                            }
                        }
                    }
                });
            }

            if (!isFormValid) {
                showToast('Veuillez corriger les erreurs dans le formulaire', 'warning');
                return;
            }
            
            currentSaveHandler(formData);
        });
    }

    document.querySelectorAll('[data-modal]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const type = this.getAttribute('data-modal');
            if (type) openModal(type);
        });
    });

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content');
        const footer = document.getElementById('footer') || document.querySelector('.footer');
        if (sidebar) {
            sidebar.classList.toggle('collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');
            if (mainContent) mainContent.classList.toggle('expanded', isCollapsed);
            if (footer) footer.classList.toggle('expanded', isCollapsed);
            try {
                localStorage.setItem('geicg_sidebar_collapsed', isCollapsed ? '1' : '0');
            } catch (e) {}
            if (window.lucide) {
                lucide.createIcons();
            }
        }
    }

    try {
        if (localStorage.getItem('geicg_sidebar_collapsed') === '1') {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            const footer = document.getElementById('footer') || document.querySelector('.footer');
            if (sidebar) sidebar.classList.add('collapsed');
            if (mainContent) mainContent.classList.add('expanded');
            if (footer) footer.classList.add('expanded');
        }
    } catch (e) {}

    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }

    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');
    if (mobileMenuBtn && sidebar) {
        function openMobileSidebar() {
            sidebar.classList.add('mobile-open');
            let backdrop = document.getElementById('sidebarBackdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'sidebar-backdrop';
                backdrop.id = 'sidebarBackdrop';
                document.body.appendChild(backdrop);
            }
            document.body.style.overflow = 'hidden';
            backdrop.addEventListener('click', closeMobileSidebar);
        }

        function closeMobileSidebar() {
            sidebar.classList.remove('mobile-open');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (backdrop) backdrop.remove();
            document.body.style.overflow = '';
        }

        mobileMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (sidebar.classList.contains('mobile-open')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('mobile-open')) {
                closeMobileSidebar();
            }
        });
    }

    const searchToggle = document.getElementById('searchToggle');
    const searchMobile = document.getElementById('searchMobile');
    if (searchToggle && searchMobile) {
        searchToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const isActive = searchMobile.classList.contains('active');
            document.querySelectorAll('.search-wrapper--mobile').forEach(function(el) { el.classList.remove('active'); });
            if (!isActive) {
                searchMobile.classList.add('active');
                const input = searchMobile.querySelector('#globalSearchMobile');
                if (input) setTimeout(function() { input.focus(); }, 100);
            }
        });
    }

    ['notification', 'profile', 'quickActions'].forEach(function(name) {
        const btn = document.getElementById(name + 'Btn');
        const panel = document.getElementById(name + 'Panel');
        if (btn && panel) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                document.querySelectorAll('.dropdown-panel').forEach(function(p) {
                    if (p !== panel) p.classList.remove('active');
                });
                panel.classList.toggle('active');
            });
        }
    });

    ['bnProfil'].forEach(function(id) {
        const btn = document.getElementById(id);
        const panel = document.getElementById('panelProfil');
        if (btn && panel) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                document.querySelectorAll('.dropdown-panel').forEach(function(p) {
                    if (p !== panel) p.classList.remove('active');
                });
                panel.classList.toggle('active');
            });
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-panel') && !e.target.closest('.btn-icon') && !e.target.closest('#profileBtn') && !e.target.closest('.bottom-nav-item')) {
            document.querySelectorAll('.dropdown-panel').forEach(function(p) { p.classList.remove('active'); });
        }
    });

    function updateBottomNavActive() {
        const path = window.location.pathname.replace(/^\/(ovolias|geicg)\/?/, '/');
        document.querySelectorAll('.bottom-nav-item').forEach(function(item) {
            item.classList.remove('active');
            const href = item.getAttribute('href') || '';
            const cleanHref = href.replace(/^\/(ovolias|geicg)\/?/, '/');
            if (path === cleanHref || path.startsWith(cleanHref + '/')) {
                item.classList.add('active');
            }
        });
    }

    updateBottomNavActive();

    function escapeHtml(str) {
        return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
});
