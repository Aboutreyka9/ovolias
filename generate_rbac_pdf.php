<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/Database.php';

use Dompdf\Dompdf;
use Dompdf\Options;

try {
    $db = (new Database())->getCon();

    // 1. Récupération des Utilisateurs et leurs Rôles
    $stmtUsers = $db->query("
        SELECT u.code_user, u.nom_user, u.prenom_user, u.email_user, u.statut_user, ur.role_code, r.libelle_role
        FROM users u
        LEFT JOIN user_roles ur ON u.code_user = ur.user_code
        LEFT JOIN roles r ON ur.role_code = r.code_role
        ORDER BY u.id_user ASC
    ");
    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    // 2. Récupération des Rôles
    $stmtRoles = $db->query("
        SELECT id, code_role, libelle_role, module, groupe, description
        FROM roles
        ORDER BY id ASC
    ");
    $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

    // 3. Récupération des Permissions par Module
    $stmtPerms = $db->query("
        SELECT module_permission, code_permission, libelle_permission
        FROM permissions
        ORDER BY module_permission ASC, code_permission ASC
    ");
    $permissionsRaw = $stmtPerms->fetchAll(PDO::FETCH_ASSOC);
    $permissionsByModule = [];
    foreach ($permissionsRaw as $p) {
        $permissionsByModule[$p['module_permission']][] = $p;
    }

    // 4. Récupération des associations Role-Permission
    $stmtRolePerms = $db->query("
        SELECT rp.role_code, rp.permission_code, p.libelle_permission, p.module_permission
        FROM role_permissions rp
        JOIN permissions p ON rp.permission_code = p.code_permission
    ");
    $rolePermsRaw = $stmtRolePerms->fetchAll(PDO::FETCH_ASSOC);
    $rolePermissionsDetails = [];
    $rolePermissionsMap = [];
    foreach ($rolePermsRaw as $rp) {
        $rolePermissionsMap[$rp['role_code']][] = $rp['permission_code'];
        $rolePermissionsDetails[$rp['role_code']][] = $rp;
    }

    // Dictionnaire détaillé des actions concrètes autorisées et restreintes par rôle
    $roleActionsDetail = [
        'ROLE_SUPERADMIN' => [
            'actions_autorisees' => [
                'Accès total et sans restriction à l’intégralité des modules, vues et API du système OVOLIA Aviculture.',
                'Création, modification, réinitialisation de mot de passe et désactivation de n’importe quel compte utilisateur.',
                'Gestion complète de la matrice RBAC (Créer des rôles, créer des permissions granulaires, attribuer des droits).',
                'Configuration des paramètres système maîtres (Nom d’établissement, logo, monnaie, zones géographiques).',
                'Supervision absolue de toutes les opérations financières, des ventes POS, des stocks et de la rentabilité.',
                'Accès aux scripts de sauvegarde, de restauration et aux journaux d’audit de sécurité.'
            ],
            'actions_restreintes' => [
                'Aucune restriction technique (Rôle maître système).'
            ]
        ],
        'ROLE_ADMIN' => [
            'actions_autorisees' => [
                'Gestion administrative des utilisateurs (Création de comptes, affectation de rôles, modification des coordonnées).',
                'Attribution et ajustement des permissions aux rôles existants via l’interface RBAC.',
                'Configuration et mise à jour des zones géographiques de livraison et des fiches établissements.',
                'Consultation globale de tous les tableaux de bord (Ventes, Stocks, Achats, Dépenses).',
                'Extraction des rapports d’activité et contrôle des accès système.'
            ],
            'actions_restreintes' => [
                'Ne peut pas modifier ni supprimer le compte Super Administrateur racine.',
                'Ne peut pas contourner les règles d’intégrité financière sans permission explicite.'
            ]
        ],
        'ROLE_DIR_GENERAL' => [
            'actions_autorisees' => [
                'Consultation en lecture globale de l’ensemble des tableaux de bord exécutifs et indicateurs de rentabilité.',
                'Visionneuse complète des rapports de vente avicole par gamme de poids, par zone et par période.',
                'Consultation de l’état des stocks en temps réel, des mouvements de volailles et des pesées.',
                'Consultation du journal des dépenses d’exploitation et des marges bénéficiaires.',
                'Accès aux listes des clients avicoles et des fournisseurs stratégiques.'
            ],
            'actions_restreintes' => [
                'Pas de saisie opérationnelle directe au comptoir de vente POS.',
                'Pas de modification directe de la matrice de sécurité RBAC.'
            ]
        ],
        'ROLE_COMMERCIAL' => [
            'actions_autorisees' => [
                'Saisie et enregistrement direct des ventes avicoles au comptoir (Caisse POS) avec choix de la catégorie de poids.',
                'Émission et impression des reçus / tickets de vente pour les clients.',
                'Création et mise à jour des fiches clients avicoles dans le répertoire commercial.',
                'Consultation du catalogue des produits avicoles et des grilles tarifaires de poids en vigueur.',
                'Enregistrement des demandes d’expéditions et de livraison pour les clients.'
            ],
            'actions_restreintes' => [
                'Ne peut pas modifier les prix du kilo ni changer les grilles de tarifs de poids.',
                'Ne peut pas saisir d’achats d’intrants auprès des fournisseurs.',
                'Ne peut pas valider ou modifier des dépenses d’exploitation.'
            ]
        ],
        'ROLE_CAISSIER' => [
            'actions_autorisees' => [
                'Enregistrement des encaissements des ventes avicoles au comptoir (POS).',
                'Sélection des produits et pesées pour édition immédiate de la facture / ticket de caisse.',
                'Consultation du catalogue des prix et des catégories de poids avicoles.',
                'Impression des justificatifs d’encaissement pour le client.'
            ],
            'actions_restreintes' => [
                'Ne peut pas modifier le fichier des utilisateurs ni toucher à la configuration RBAC.',
                'Ne peut pas valider les réceptions d’achats fournisseurs ni ordonnancer des dépenses.'
            ]
        ],
        'ROLE_GESTIONNAIRE' => [
            'actions_autorisees' => [
                'Gestion du catalogue des produits avicoles (Poulets de chair, Pintades, Pondeuses, Œufs, Aliments).',
                'Création et paramétrage des catégories de poids (ex: 1.2kg-1.5kg, 1.5kg-1.8kg, 2.0kg+).',
                'Saisie des pesées réelles au lot et impression des étiquettes à code-barres / poids net.',
                'Saisie des réceptions d’achats avicoles (approvisionnements poussins/volailles/aliments).',
                'Enregistrement des mouvements de stock (Entrées, Sorties, Perte/Mortalité, Transferts).',
                'Planification des livraisons, édition des Bons de Livraison (BL) et gestion des véhicules de transport.'
            ],
            'actions_restreintes' => [
                'Ne peut pas ordonnancer ni approuver des dépenses financières.',
                'Ne me peut pas créer ni modifier des comptes utilisateurs ou des rôles.'
            ]
        ],
        'ROLE_FINANCE' => [
            'actions_autorisees' => [
                'Saisie, ordonnancement et validation des dépenses d’exploitation avicole (Alimentation, Soins, Énergie, Transport).',
                'Gestion et création des catégories et types de dépenses.',
                'Consultation et analyse des rapports de rentabilité financière par gamme de poids et par lot.',
                'Validation de la valorisation comptable du stock et contrôle des écarts de pesée.',
                'Suivi des encaissements de ventes et validation des règlements d’achats aux fournisseurs.'
            ],
            'actions_restreintes' => [
                'Ne peut pas modifier le code source ou la matrice RBAC des utilisateurs.',
                'Ne peut pas altérer les pesées réelles sans laisser de trace d’écart auditée.'
            ]
        ],
        'ROLE_COMPTABLE' => [
            'actions_autorisees' => [
                'Saisie des pièces comptables et imputation des charges d’exploitation.',
                'Consultation du journal des ventes avicoles et des règlements d’achats.',
                'Extraction des bilans d’exploitation et rapports de rentabilité globale.'
            ],
            'actions_restreintes' => [
                'Ne peut pas modifier les grilles tarifaires de poids des produits.',
                'Ne peut pas gérer les comptes utilisateurs ou réinitialiser des accès.'
            ]
        ]
    ];

    // --- CONSTRUCTION DU HTML PDF ---
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Guide d'Utilisation & Spécification RBAC - OVOLIA Aviculture</title>
        <style>
            @page {
                margin: 25px 30px 40px 30px;
            }
            body {
                font-family: 'Helvetica', 'Arial', sans-serif;
                color: #1E293B;
                font-size: 10px;
                line-height: 1.45;
            }
            
            /* En-tête / Header */
            .header-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 12px;
                border-bottom: 3px solid #1E3A5F;
                padding-bottom: 6px;
            }
            .header-title {
                color: #1E3A5F;
                font-size: 18px;
                font-weight: bold;
                text-transform: uppercase;
                margin: 0;
            }
            .header-subtitle {
                color: #059669;
                font-size: 11px;
                font-weight: bold;
                margin-top: 2px;
            }
            .header-date {
                text-align: right;
                font-size: 9px;
                color: #64748B;
            }

            /* Section titles */
            .section-title {
                background: #1E3A5F;
                color: #FFFFFF;
                padding: 5px 10px;
                font-size: 12px;
                font-weight: bold;
                border-radius: 4px;
                margin-top: 16px;
                margin-bottom: 8px;
                text-transform: uppercase;
            }
            .subsection-title {
                color: #059669;
                font-size: 11px;
                font-weight: bold;
                margin-top: 12px;
                margin-bottom: 5px;
                border-bottom: 1.5px solid #E2E8F0;
                padding-bottom: 2px;
            }

            /* Card styles */
            .intro-card {
                background: #F8FAFC;
                border-left: 4px solid #059669;
                padding: 8px 12px;
                border-radius: 4px;
                margin-bottom: 10px;
            }

            .role-box {
                border: 1px solid #CBD5E1;
                border-radius: 4px;
                padding: 8px 10px;
                margin-bottom: 10px;
                background: #FFFFFF;
            }
            .role-box-header {
                background: #F1F5F9;
                margin: -8px -10px 6px -10px;
                padding: 5px 10px;
                border-bottom: 1px solid #CBD5E1;
                border-radius: 3px 3px 0 0;
            }

            /* Menu Box Guide */
            .menu-guide-box {
                background: #EFF6FF;
                border: 1px solid #BFDBFE;
                border-left: 4px solid #2563EB;
                border-radius: 4px;
                padding: 8px 10px;
                margin-bottom: 10px;
            }
            .menu-path {
                color: #1E40AF;
                font-weight: bold;
                font-size: 10px;
            }

            /* Tables */
            table.data-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }
            table.data-table th {
                background: #F1F5F9;
                color: #334155;
                font-weight: bold;
                font-size: 9px;
                text-align: left;
                padding: 5px 7px;
                border: 1px solid #CBD5E1;
                text-transform: uppercase;
            }
            table.data-table td {
                padding: 5px 7px;
                border: 1px solid #E2E8F0;
                font-size: 9px;
                vertical-align: top;
            }
            table.data-table tr:nth-child(even) {
                background: #F8FAFC;
            }

            /* Badges */
            .badge {
                display: inline-block;
                padding: 2px 5px;
                font-size: 8px;
                font-weight: bold;
                border-radius: 3px;
                color: #FFFFFF;
            }
            .badge-role { background: #1E3A5F; }
            .badge-active { background: #059669; }
            .badge-module { background: #475569; }
            .badge-perm { background: #0284C7; }
            .badge-menu { background: #2563EB; }

            .list-actions {
                margin: 3px 0 5px 12px;
                padding: 0;
            }
            .list-actions li {
                margin-bottom: 2px;
            }

            .text-allowed { color: #047857; font-weight: bold; }
            .text-restricted { color: #B91C1C; font-weight: bold; }

            .page-break {
                page-break-after: always;
            }

            /* Footer */
            footer {
                position: fixed;
                bottom: -20px;
                left: 0;
                right: 0;
                height: 25px;
                text-align: center;
                font-size: 8px;
                color: #94A3B8;
                border-top: 1px solid #E2E8F0;
                padding-top: 4px;
            }
        </style>
    </head>
    <body>
        <footer>
            OVOLIA AVICULTURE — Manuel d'Utilisation & Spécification RBAC | Page <script type="text/php">echo $pdf->get_page_number();</script> / <script type="text/php">echo $pdf->get_page_count();</script>
        </footer>

        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td>
                    <div class="header-title">OVOLIA AVICULTURE</div>
                    <div class="header-subtitle">Manuel d'Utilisation Officiel & Emplacement des Menus par Action</div>
                </td>
                <td class="header-date">
                    Date d'édition : <?= date('d/m/Y') ?><br>
                    Version : 3.0 (Manuel Opérationnel Complet)
                </td>
            </tr>
        </table>

        <!-- INTRODUCTION -->
        <div class="intro-card">
            <strong>Guide d'Utilisation de l'Application OVOLIA Aviculture :</strong><br>
            Ce manuel est destiné aux utilisateurs et administrateurs du système <strong>OVOLIA Aviculture</strong>. Il détaille pas à pas l'utilisation de l'application, l'emplacement exact dans le <strong>menu latéral de navigation</strong> pour chaque action métier, ainsi que la matrice des rôles (RBAC) garantissant la sécurité des données.
        </div>

        <!-- SECTION 1 : MANUEL D'UTILISATION ET EMPLACEMENT DES MENUS -->
        <div class="section-title">1. Guide d'Utilisation Opérationnel & Cartographie des Menus</div>
        <p>L'application s'organise autour d'un menu latéral repliable divisé en 6 grands modules métiers. Retrouvez ci-dessous la démarche d'utilisation et les emplacements exacts :</p>

        <!-- 1.1 TABLEAU DE BORD -->
        <div class="menu-guide-box">
            <table style="width: 100%;">
                <tr>
                    <td><strong style="font-size: 11px; color: #1E3A5F;">1.1 Tableau de Bord Principal (Dashboard)</strong></td>
                    <td style="text-align: right;"><span class="badge badge-menu">Menu Latéral &rarr; Tableau de bord</span></td>
                </tr>
            </table>
            <div class="menu-path" style="margin-top: 3px;">📍 Emplacement : Racine du site (clic sur le logo OVOLIA ou sur "Tableau de bord")</div>
            <ul class="list-actions">
                <li><strong>Visualisation globale des KPI :</strong> Consultation du Chiffre d'Affaires du jour, du volume de ventes avicoles en kg et pièces, des entrées en stock et des dépenses d'exploitation.</li>
                <li><strong>Raccourcis rapides :</strong> Accès direct vers la caisse de vente POS, la saisie des pesées et les rapports.</li>
                <li><strong>Accès :</strong> Autorisé à tous les utilisateurs connectés (avec affichage adapté au rôle).</li>
            </ul>
        </div>

        <!-- 1.2 ESPACE COMMERCIAL ET VENTES -->
        <div class="menu-guide-box">
            <table style="width: 100%;">
                <tr>
                    <td><strong style="font-size: 11px; color: #1E3A5F;">1.2 Espace Commercial & Caisse POS Ventes</strong></td>
                    <td style="text-align: right;"><span class="badge badge-menu">Menu Latéral &rarr; Espace Commercial & Ventes</span></td>
                </tr>
            </table>
            <ul class="list-actions">
                <li>
                    <strong>📍 Emplacement : `Espace Commercial & Ventes` &rarr; `Clients Avicoles` (`/aviculture/clients`)</strong><br>
                    <em>Action :</em> Créer un nouveau client (Nom, Téléphone, Adresse, Zone), modifier les informations d'un client et consulter son historique d'achats.
                </li>
                <li>
                    <strong>📍 Emplacement : `Espace Commercial & Ventes` &rarr; `Caisse POS Ventes` (`/aviculture/ventes`)</strong><br>
                    <em>Action :</em> Ouvrir le terminal de caisse POS, sélectionner les volailles (Poulets, Pintades) ou œufs, choisir la catégorie de poids, appliquer le tarif au kilo, encaisser (Espèces, Mobile Money) et imprimer le ticket de vente.
                </li>
                <li>
                    <strong>📍 Emplacement : `Espace Commercial & Ventes` &rarr; `Grille Poids & Tarifs` (`/aviculture/categories_poids`)</strong><br>
                    <em>Action :</em> Consulter la grille officielle des catégories de poids et les prix au kg fixés par la direction.
                </li>
            </ul>
        </div>

        <!-- 1.3 CATALOGUE ET PESÉES -->
        <div class="menu-guide-box">
            <table style="width: 100%;">
                <tr>
                    <td><strong style="font-size: 11px; color: #1E3A5F;">1.3 Catalogue des Produits & Saisie des Pesées</strong></td>
                    <td style="text-align: right;"><span class="badge badge-menu">Menu Latéral &rarr; Catalogue & Pesées</span></td>
                </tr>
            </table>
            <ul class="list-actions">
                <li>
                    <strong>📍 Emplacement : `Catalogue & Pesées` &rarr; `Produits Avicoles` (`/aviculture/produits`)</strong><br>
                    <em>Action :</em> Enregistrer un nouveau produit au catalogue (Poulet de chair, Pintade, Poussin d'un jour, Casier d'œufs), fixer l'unité de mesure (Kg ou Pièce) et activer/désactiver les articles.
                </li>
                <li>
                    <strong>📍 Emplacement : `Catalogue & Pesées` &rarr; `Grille Poids & Tarifs` (`/aviculture/categories_poids`)</strong><br>
                    <em>Action :</em> Configurer les tranches de calibrage de poids (ex: Tranche A: 1.2kg à 1.5kg, Tranche B: 1.5kg à 1.8kg) et attribuer les tarifs unitaires ou au kilo.
                </li>
                <li>
                    <strong>📍 Emplacement : `Catalogue & Pesées` &rarr; `Pesées & Étiquettes` (`/aviculture/pesees`)</strong><br>
                    <em>Action :</em> Enregistrer la pesée réelle au gramme près d'un lot de volailles à la réception ou avant mise en vente, générer et imprimer l'étiquette avec code-barres / QR Code de poids net.
                </li>
            </ul>
        </div>

        <div class="page-break"></div>

        <!-- 1.4 LOGISTIQUE ACHATS ET STOCKS -->
        <div class="menu-guide-box">
            <table style="width: 100%;">
                <tr>
                    <td><strong style="font-size: 11px; color: #1E3A5F;">1.4 Logistique, Approvisionnements & Gestion des Stocks</strong></td>
                    <td style="text-align: right;"><span class="badge badge-menu">Menu Latéral &rarr; Logistique & Stock</span></td>
                </tr>
            </table>
            <ul class="list-actions">
                <li>
                    <strong>📍 Emplacement : `Logistique & Stock` &rarr; `Expéditions & Livraisons` (`/aviculture/livraisons`)</strong><br>
                    <em>Action :</em> Enregistrer une livraison client, affecter un chauffeur et un véhicule de transport, générer le Bon de Livraison (BL) et marquer la livraison comme "Livrée".
                </li>
                <li>
                    <strong>📍 Emplacement : `Logistique & Stock` &rarr; `Flotte de Véhicules` (`/aviculture/vehicules`)</strong><br>
                    <em>Action :</em> Ajouter et suivre les véhicules de livraison (Camionnettes, Tricycles, Motos) et leurs immatriculations.
                </li>
                <li>
                    <strong>📍 Emplacement : `Logistique & Stock` &rarr; `État des Stocks` (`/aviculture/stock`)</strong><br>
                    <em>Action :</em> Consulter l'inventaire en temps réel par catégorie de produit et de poids (Poids total disponible en kg, quantité en pièces, valeur estimée).
                </li>
                <li>
                    <strong>📍 Emplacement : `Logistique & Stock` &rarr; `Mouvements de Stock` (`/aviculture/mouvements_stock`)</strong><br>
                    <em>Action :</em> Consulter le journal historique d'audit des mouvements (Entrées sur achat, Sorties sur vente, Pertes/Mortalités, Ajustements d'inventaire).
                </li>
                <li>
                    <strong>📍 Emplacement : `Logistique & Stock` &rarr; `Achats & Approvisionnements` (`/aviculture/achats`)</strong><br>
                    <em>Action :</em> Enregistrer un bon d'achat d'intrants (Poussins, Aliments, Volailles prêtes à la vente), saisir le fournisseur, la quantité reçue et le montant facturé.
                </li>
                <li>
                    <strong>📍 Emplacement : `Logistique & Stock` &rarr; `Fournisseurs Avicoles` (`/aviculture/fournisseurs`)</strong><br>
                    <em>Action :</em> Gérer le répertoire des fournisseurs d'intrants avicoles (Nom, Téléphone, Adresse, Solde dû).
                </li>
            </ul>
        </div>

        <!-- 1.5 FINANCES ET DEPENSES -->
        <div class="menu-guide-box">
            <table style="width: 100%;">
                <tr>
                    <td><strong style="font-size: 11px; color: #1E3A5F;">1.5 Finances, Trésorerie & Dépenses d'Exploitation</strong></td>
                    <td style="text-align: right;"><span class="badge badge-menu">Menu Latéral &rarr; Finances & Trésorerie</span></td>
                </tr>
            </table>
            <ul class="list-actions">
                <li>
                    <strong>📍 Emplacement : `Finances & Trésorerie` &rarr; `Ventes & Encaissements` (`/aviculture/ventes`)</strong><br>
                    <em>Action :</em> Supervision comptable du journal des encaissements de ventes réalisés en caisse.
                </li>
                <li>
                    <strong>📍 Emplacement : `Finances & Trésorerie` &rarr; `Achats & Règlements` (`/aviculture/achats`)</strong><br>
                    <em>Action :</em> Suivi des règlements d'achats effectués aux fournisseurs d'intrants.
                </li>
                <li>
                    <strong>📍 Emplacement : `Finances & Trésorerie` &rarr; `Dépenses Exploitation` (`/depense/list`)</strong><br>
                    <em>Action :</em> Enregistrer une dépense d'exploitation (Achat d'aliments, soins vétérinaires, électricité, loyer, carburant), téléverser la pièce justificative et valider le paiement.
                </li>
                <li>
                    <strong>📍 Emplacement : `Finances & Trésorerie` &rarr; `Types de Dépenses` (`/type_depense/list`)</strong><br>
                    <em>Action :</em> Créer et organiser les rubriques comptables de dépenses pour l'analyse des coûts de revient.
                </li>
            </ul>
        </div>

        <!-- 1.6 ADMINISTRATION ET RBAC -->
        <div class="menu-guide-box">
            <table style="width: 100%;">
                <tr>
                    <td><strong style="font-size: 11px; color: #1E3A5F;">1.6 Administration Système, Sécurité RBAC & Configuration</strong></td>
                    <td style="text-align: right;"><span class="badge badge-menu">Menu Latéral &rarr; Administration & Configuration</span></td>
                </tr>
            </table>
            <ul class="list-actions">
                <li>
                    <strong>📍 Emplacement : `Administration & RBAC` &rarr; `Utilisateurs Système` (`/user/list`)</strong><br>
                    <em>Action :</em> Créer des comptes utilisateurs, attribuer un rôle principal, réinitialiser des mots de passe et activer/désactiver des comptes.
                </li>
                <li>
                    <strong>📍 Emplacement : `Administration & RBAC` &rarr; `Rôles RBAC` (`/role/list`)</strong><br>
                    <em>Action :</em> Consulter et gérer les 8 rôles système de l'application (`ROLE_COMMERCIAL`, `ROLE_GESTIONNAIRE`, etc.).
                </li>
                <li>
                    <strong>📍 Emplacement : `Administration & RBAC` &rarr; `Permissions Granulaires` (`/permission/list`)</strong><br>
                    <em>Action :</em> Attribuer ou révoquer les 30 permissions granulaires par rôle.
                </li>
                <li>
                    <strong>📍 Emplacement : `Configuration Système` &rarr; `Zones Géographiques` (`/zone/list`)</strong><br>
                    <em>Action :</em> Gérer les zones géographiques de livraison et de vente.
                </li>
                <li>
                    <strong>📍 Emplacement : `Configuration Système` &rarr; `Établissements` (`/etablissement/config`)</strong><br>
                    <em>Action :</em> Configurer les informations de l'établissement (Nom, logo officiel, téléphone, adresse, pied de page des tickets de caisse).
                </li>
            </ul>
        </div>

        <div class="page-break"></div>

        <!-- SECTION 2 : COMPTES UTILISATEURS ACTIFS -->
        <div class="section-title">2. Comptes Utilisateurs Actifs & Rôles</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Code User</th>
                    <th style="width: 25%;">Nom & Prénom</th>
                    <th style="width: 30%;">Email Utilisateur</th>
                    <th style="width: 15%;">Rôle Assigné</th>
                    <th style="width: 10%;">Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['code_user']) ?></strong></td>
                    <td><?= htmlspecialchars($u['nom_user'] . ' ' . $u['prenom_user']) ?></td>
                    <td><?= htmlspecialchars($u['email_user']) ?></td>
                    <td><span class="badge badge-role"><?= htmlspecialchars($u['role_code'] ?? 'NON ATTR') ?></span></td>
                    <td><span class="badge badge-active"><?= strtoupper(htmlspecialchars($u['statut_user'])) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- SECTION 3 : DESCRIPTIONS ET ACTIONS PAR ROLE -->
        <div class="section-title">3. Fiches Détaillées des Rôles & Habilitations</div>
        
        <?php foreach ($roles as $r): ?>
            <?php 
                $rCode = $r['code_role']; 
                $detail = $roleActionsDetail[$rCode] ?? [
                    'actions_autorisees' => ['Accès selon les permissions assignées.'],
                    'actions_restreintes' => ['Restreint aux modules autorisés.']
                ];
                $perms = $rolePermissionsDetails[$rCode] ?? [];
            ?>
            <div class="role-box">
                <div class="role-box-header">
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <strong style="font-size: 11px; color: #1E3A5F;"><?= htmlspecialchars($r['libelle_role']) ?></strong> 
                                <span class="badge badge-role"><?= htmlspecialchars($rCode) ?></span>
                            </td>
                            <td style="text-align: right;">
                                Domaine : <span class="badge badge-module"><?= htmlspecialchars($r['module']) ?></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="margin-bottom: 5px;">
                    <strong>Description :</strong> <?= htmlspecialchars($r['description'] ?? 'Périmètre d\'action spécifique.') ?>
                </div>

                <div class="text-allowed">✔ Actions Concrètes Autorisées :</div>
                <ul class="list-actions">
                    <?php foreach ($detail['actions_autorisees'] as $act): ?>
                        <li><?= htmlspecialchars($act) ?></li>
                    <?php endforeach; ?>
                </ul>

                <div class="text-restricted">✖ Actions Restreintes / Non Autorisées :</div>
                <ul class="list-actions">
                    <?php foreach ($detail['actions_restreintes'] as $rest): ?>
                        <li><?= htmlspecialchars($rest) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

        <!-- SECTION 4 : INVENTAIRE DES 30 PERMISSIONS -->
        <div class="section-title">4. Matrice des 30 Permissions Granulaires par Module</div>

        <?php foreach ($permissionsByModule as $moduleName => $pList): ?>
            <div class="subsection-title">Module : <?= htmlspecialchars($moduleName) ?> (<?= count($pList) ?> Permissions)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 32%;">Code Permission</th>
                        <th style="width: 48%;">Action & Fonctionnalité Autorisée</th>
                        <th style="width: 20%;">Rôles Associés</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pList as $p): ?>
                    <?php 
                        $associatedRoles = [];
                        foreach ($rolePermissionsMap as $roleCode => $pArray) {
                            if (in_array($p['code_permission'], $pArray, true)) {
                                $associatedRoles[] = $roleCode;
                            }
                        }
                    ?>
                    <tr>
                        <td><strong><span class="badge badge-perm"><?= htmlspecialchars($p['code_permission']) ?></span></strong></td>
                        <td><?= htmlspecialchars($p['libelle_permission']) ?></td>
                        <td>
                            <?php if (in_array('ROLE_SUPERADMIN', $associatedRoles, true) && count($associatedRoles) >= 6): ?>
                                <em style="color: #059669; font-weight: bold;">Rôles Admins & Métiers</em>
                            <?php else: ?>
                                <?= htmlspecialchars(implode(', ', $associatedRoles)) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

    </body>
    </html>
    <?php
    $html = ob_get_clean();

    // Render with Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $outputFile = __DIR__ . '/description_rbac.pdf';
    file_put_contents($outputFile, $dompdf->output());

    echo "[SUCCESS] Le manuel complet et la matrice RBAC ont été générés dans 'description_rbac.pdf' ! Taille : " . filesize($outputFile) . " octets.\n";

} catch (Exception $e) {
    echo "[ERROR] Échec de la génération du PDF : " . $e->getMessage() . "\n";
}
