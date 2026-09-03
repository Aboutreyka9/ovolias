<?php 
require_once __DIR__ . '/../core/PrincipalRoute.php';

$route = new Router();

// -------------------------------------------------------------
// Route d'accueil & Authentification
// -------------------------------------------------------------
$route->addRoute('/', ['HomeController', 'index']);
$route->addRoute('/home/dashboardData', ['HomeController', 'dashboardData']);
$route->addRoute('/user/connexion', ['UserController', 'connexion']);
$route->addRoute('/user/activer', ['UserController', 'activer']);
$route->addRoute('/user/forgot_password', ['UserController', 'forgotPassword']);
$route->addRoute('/user/reset_password', ['UserController', 'resetPassword']);
$route->addRoute('/user/decon', ['UserController', 'decon']);
$route->addRoute('/user/logout', ['UserController', 'logout']);
$route->addRoute('/user/profil', ['UserController', 'profil']);
$route->addRoute('/user/editPassword', ['UserController', 'editPassword']);
$route->addRoute('/user/list', ['UserController', 'list']);
$route->addRoute('/user/apiList', ['UserController', 'apiList']);
$route->addRoute('/user/add', ['UserController', 'add']);
$route->addRoute('/user/edit', ['UserController', 'edit']);
$route->addRoute('/user/changer', ['UserController', 'changer']);
$route->addRoute('/user/edition/{param}', ['UserController', 'edition']);
$route->addRoute('/user/details/{param}', ['UserController', 'details']);
$route->addRoute('/user/formulaire', ['UserController', 'formulaire']);

// -------------------------------------------------------------
// Module: Etablissements & Fonctions
// -------------------------------------------------------------
$route->addRoute('/etablissement/config', ['EtablissementController', 'config']);
$route->addRoute('/etablissement/list', ['EtablissementController', 'list']);
$route->addRoute('/etablissement/apiList', ['EtablissementController', 'apiList']);
$route->addRoute('/etablissement/add', ['EtablissementController', 'add']);
$route->addRoute('/etablissement/edit', ['EtablissementController', 'edit']);
$route->addRoute('/etablissement/changer', ['EtablissementController', 'changer']);
$route->addRoute('/etablissement/details/{param}', ['EtablissementController', 'details']);
$route->addRoute('/etablissement/edition/{param}', ['EtablissementController', 'edition']);
$route->addRoute('/etablissement/formulaire', ['EtablissementController', 'formulaire']);

$route->addRoute('/fonction/list', ['FonctionController', 'list']);
$route->addRoute('/fonction/apiList', ['FonctionController', 'apiList']);
$route->addRoute('/fonction/add', ['FonctionController', 'add']);
$route->addRoute('/fonction/edit', ['FonctionController', 'edit']);
$route->addRoute('/fonction/changer', ['FonctionController', 'changer']);
$route->addRoute('/fonction/details/{param}', ['FonctionController', 'details']);
$route->addRoute('/fonction/edition/{param}', ['FonctionController', 'edition']);
$route->addRoute('/fonction/formulaire', ['FonctionController', 'formulaire']);

// -------------------------------------------------------------
// Module: Configuration (Années, Sessions, Zones)
// -------------------------------------------------------------
$route->addRoute('/annee/list', ['AnneeController', 'list']);
$route->addRoute('/annee/apiList', ['AnneeController', 'apiList']);
$route->addRoute('/annee/add', ['AnneeController', 'add']);
$route->addRoute('/annee/edit', ['AnneeController', 'edit']);
$route->addRoute('/annee/changer', ['AnneeController', 'changer']);
$route->addRoute('/annee/details/{param}', ['AnneeController', 'details']);
$route->addRoute('/annee/edition/{param}', ['AnneeController', 'edition']);
$route->addRoute('/annee/formulaire', ['AnneeController', 'formulaire']);

$route->addRoute('/session/list', ['SessionController', 'list']);
$route->addRoute('/session/apiList', ['SessionController', 'apiList']);
$route->addRoute('/session/add', ['SessionController', 'add']);
$route->addRoute('/session/edit', ['SessionController', 'edit']);
$route->addRoute('/session/changer', ['SessionController', 'changer']);
$route->addRoute('/session/details/{param}', ['SessionController', 'details']);
$route->addRoute('/session/edition/{param}', ['SessionController', 'edition']);
$route->addRoute('/session/formulaire', ['SessionController', 'formulaire']);

$route->addRoute('/zone/list', ['ZoneController', 'list']);
$route->addRoute('/zone/apiList', ['ZoneController', 'apiList']);
$route->addRoute('/zone/add', ['ZoneController', 'add']);
$route->addRoute('/zone/edit', ['ZoneController', 'edit']);
$route->addRoute('/zone/changer', ['ZoneController', 'changer']);
$route->addRoute('/zone/details/{param}', ['ZoneController', 'details']);
$route->addRoute('/zone/edition/{param}', ['ZoneController', 'edition']);
$route->addRoute('/zone/formulaire', ['ZoneController', 'formulaire']);

// -------------------------------------------------------------
// Module: Catalogue & Produit (Articles, Catégories, Packs)
// -------------------------------------------------------------
$route->addRoute('/categories_articles/list', ['CategorieArticleController', 'list']);
$route->addRoute('/categories_articles/apiList', ['CategorieArticleController', 'apiList']);
$route->addRoute('/categories_articles/add', ['CategorieArticleController', 'add']);
$route->addRoute('/categories_articles/edit', ['CategorieArticleController', 'edit']);
$route->addRoute('/categories_articles/changer', ['CategorieArticleController', 'changer']);
$route->addRoute('/categories_articles/details/{param}', ['CategorieArticleController', 'details']);
$route->addRoute('/categories_articles/edition/{param}', ['CategorieArticleController', 'edition']);
$route->addRoute('/categories_articles/formulaire', ['CategorieArticleController', 'formulaire']);

$route->addRoute('/article/list', ['ArticleController', 'list']);
$route->addRoute('/article/apiList', ['ArticleController', 'apiList']);
$route->addRoute('/article/add', ['ArticleController', 'add']);
$route->addRoute('/article/edit', ['ArticleController', 'edit']);
$route->addRoute('/article/changer', ['ArticleController', 'changer']);
$route->addRoute('/article/details/{param}', ['ArticleController', 'details']);
$route->addRoute('/article/edition/{param}', ['ArticleController', 'edition']);
$route->addRoute('/article/formulaire', ['ArticleController', 'formulaire']);

$route->addRoute('/categorie_pack/list', ['CategoriePackController', 'list']);
$route->addRoute('/categorie_pack/apiList', ['CategoriePackController', 'apiList']);
$route->addRoute('/categorie_pack/add', ['CategoriePackController', 'add']);
$route->addRoute('/categorie_pack/edit', ['CategoriePackController', 'edit']);
$route->addRoute('/categorie_pack/changer', ['CategoriePackController', 'changer']);
$route->addRoute('/categorie_pack/details/{param}', ['CategoriePackController', 'details']);
$route->addRoute('/categorie_pack/edition/{param}', ['CategoriePackController', 'edition']);
$route->addRoute('/categorie_pack/formulaire', ['CategoriePackController', 'formulaire']);

$route->addRoute('/pack/list', ['PackController', 'list']);
$route->addRoute('/pack/apiList', ['PackController', 'apiList']);
$route->addRoute('/pack/add', ['PackController', 'add']);
$route->addRoute('/pack/edit', ['PackController', 'edit']);
$route->addRoute('/pack/changer', ['PackController', 'changer']);
$route->addRoute('/pack/details/{param}', ['PackController', 'details']);
$route->addRoute('/pack/edition/{param}', ['PackController', 'edition']);
$route->addRoute('/pack/formulaire', ['PackController', 'formulaire']);

// -------------------------------------------------------------
// Module: Aviculture & Catalogue Produits OVOLIA
// -------------------------------------------------------------
$route->addRoute('/aviculture/produits', ['AvicultureController', 'produits']);
$route->addRoute('/aviculture/apiListProduits', ['AvicultureController', 'apiListProduits']);
$route->addRoute('/aviculture/addProduit', ['AvicultureController', 'addProduit']);
$route->addRoute('/aviculture/editProduit', ['AvicultureController', 'editProduit']);
$route->addRoute('/aviculture/categories_poids', ['AvicultureController', 'categoriesPoids']);
$route->addRoute('/aviculture/updatePrixGrille', ['AvicultureController', 'updatePrixGrille']);
$route->addRoute('/aviculture/toggleStatutGrille', ['AvicultureController', 'toggleStatutGrille']);
$route->addRoute('/aviculture/pesees', ['AvicultureController', 'pesees']);
$route->addRoute('/aviculture/apiListPesees', ['AvicultureController', 'apiListPesees']);
$route->addRoute('/aviculture/addPesee', ['AvicultureController', 'addPesee']);
$route->addRoute('/aviculture/etiquettePrint/{param}', ['AvicultureController', 'etiquettePrint']);

// Commercial & Approvisionnements Avicoles
$route->addRoute('/aviculture/clients', ['ClientAvicoleController', 'list']);
$route->addRoute('/aviculture/apiListClients', ['ClientAvicoleController', 'apiList']);
$route->addRoute('/aviculture/addClient', ['ClientAvicoleController', 'add']);
$route->addRoute('/aviculture/editClient', ['ClientAvicoleController', 'edit']);
$route->addRoute('/aviculture/changerClient', ['ClientAvicoleController', 'changer']);

$route->addRoute('/aviculture/fournisseurs', ['FournisseurAvicoleController', 'list']);
$route->addRoute('/aviculture/apiListFournisseurs', ['FournisseurAvicoleController', 'apiList']);
$route->addRoute('/aviculture/addFournisseur', ['FournisseurAvicoleController', 'add']);
$route->addRoute('/aviculture/editFournisseur', ['FournisseurAvicoleController', 'edit']);
$route->addRoute('/aviculture/changerFournisseur', ['FournisseurAvicoleController', 'changer']);

$route->addRoute('/aviculture/ventes', ['VenteAvicoleController', 'list']);
$route->addRoute('/aviculture/apiListVentes', ['VenteAvicoleController', 'apiList']);
$route->addRoute('/aviculture/addVente', ['VenteAvicoleController', 'addVente']);

$route->addRoute('/aviculture/achats', ['AchatAvicoleController', 'list']);
$route->addRoute('/aviculture/apiListAchats', ['AchatAvicoleController', 'apiList']);
$route->addRoute('/aviculture/addAchat', ['AchatAvicoleController', 'addAchat']);


// -------------------------------------------------------------
// Module: Clients & Zones Commerciales
// -------------------------------------------------------------
$route->addRoute('/client/list', ['ClientController', 'list']);
$route->addRoute('/client/apiList', ['ClientController', 'apiList']);
$route->addRoute('/client/add', ['ClientController', 'add']);
$route->addRoute('/client/edit', ['ClientController', 'edit']);
$route->addRoute('/client/changer', ['ClientController', 'changer']);
$route->addRoute('/client/details/{param}', ['ClientController', 'details']);
$route->addRoute('/client/edition/{param}', ['ClientController', 'edition']);
$route->addRoute('/client/formulaire', ['ClientController', 'formulaire']);

$route->addRoute('/zone_commercial/list', ['ZoneCommercialController', 'list']);
$route->addRoute('/zone_commercial/apiList', ['ZoneCommercialController', 'apiList']);
$route->addRoute('/zone_commercial/add', ['ZoneCommercialController', 'add']);
$route->addRoute('/zone_commercial/edit', ['ZoneCommercialController', 'edit']);
$route->addRoute('/zone_commercial/changer', ['ZoneCommercialController', 'changer']);
$route->addRoute('/zone_commercial/details/{param}', ['ZoneCommercialController', 'details']);
$route->addRoute('/zone_commercial/edition/{param}', ['ZoneCommercialController', 'edition']);
$route->addRoute('/zone_commercial/formulaire', ['ZoneCommercialController', 'formulaire']);

// -------------------------------------------------------------
// Module: Souscriptions, Cotisations & Paiements
// -------------------------------------------------------------
$route->addRoute('/souscription/list', ['SouscriptionController', 'list']);
$route->addRoute('/souscription/apiList', ['SouscriptionController', 'apiList']);
$route->addRoute('/souscription/add', ['SouscriptionController', 'add']);
$route->addRoute('/souscription/edit', ['SouscriptionController', 'edit']);
$route->addRoute('/souscription/changer', ['SouscriptionController', 'changer']);
$route->addRoute('/souscription/details/{param}', ['SouscriptionController', 'details']);
$route->addRoute('/souscription/edition/{param}', ['SouscriptionController', 'edition']);
$route->addRoute('/souscription/formulaire', ['SouscriptionController', 'formulaire']);
$route->addRoute('/souscription/wizard', ['SouscriptionController', 'wizard']);
$route->addRoute('/souscription/wizardData', ['SouscriptionController', 'wizardData']);
$route->addRoute('/souscription/wizardSubmit', ['SouscriptionController', 'wizardSubmit']);

$route->addRoute('/cotisation/list', ['CotisationController', 'list']);
$route->addRoute('/cotisation/apiList', ['CotisationController', 'apiList']);
$route->addRoute('/cotisation/add', ['CotisationController', 'add']);
$route->addRoute('/cotisation/edit', ['CotisationController', 'edit']);
$route->addRoute('/cotisation/changer', ['CotisationController', 'changer']);
$route->addRoute('/cotisation/details/{param}', ['CotisationController', 'details']);
$route->addRoute('/cotisation/edition/{param}', ['CotisationController', 'edition']);
$route->addRoute('/cotisation/formulaire', ['CotisationController', 'formulaire']);

$route->addRoute('/cautisation-payment/search-form', ['CautisationPaymentController', 'searchForm']);
$route->addRoute('/cautisation-payment/search', ['CautisationPaymentController', 'search']);
$route->addRoute('/cautisation-payment/situation', ['CautisationPaymentController', 'situation']);
$route->addRoute('/cautisation-payment/situation/{param}', ['CautisationPaymentController', 'situation']);
$route->addRoute('/cautisation-payment/situation-details', ['CautisationPaymentController', 'situationDetails']);
$route->addRoute('/cautisation-payment/history', ['CautisationPaymentController', 'history']);
$route->addRoute('/cautisation-payment/savepayment', ['CautisationPaymentController', 'savepayment']);

// -------------------------------------------------------------
// Module: Distributions
// -------------------------------------------------------------
$route->addRoute('/distribution/list', ['DistributionController', 'list']);
$route->addRoute('/distribution/apiList', ['DistributionController', 'apiList']);
$route->addRoute('/distribution/add', ['DistributionController', 'add']);
$route->addRoute('/distribution/edit', ['DistributionController', 'edit']);
$route->addRoute('/distribution/changer', ['DistributionController', 'changer']);
$route->addRoute('/distribution/details/{param}', ['DistributionController', 'details']);
$route->addRoute('/distribution/edition/{param}', ['DistributionController', 'edition']);
$route->addRoute('/distribution/formulaire', ['DistributionController', 'formulaire']);

// -------------------------------------------------------------
// Module: Caisse & Finances (Ouverture, Clôture, Dépenses, Versements)
// -------------------------------------------------------------
$route->addRoute('/caisse_commercial/list', ['CaisseCommercialController', 'list']);
$route->addRoute('/caisse_commercial/apiList', ['CaisseCommercialController', 'apiList']);
$route->addRoute('/caisse_commercial/getDailyTotals', ['CaisseCommercialController', 'getDailyTotals']);
$route->addRoute('/caisse_commercial/apiGetCommercialSession', ['CaisseCommercialController', 'apiGetCommercialSession']);
$route->addRoute('/caisse_commercial/ouvrirMaCaisse', ['CaisseCommercialController', 'ouvrirMaCaisse']);
$route->addRoute('/caisse_commercial/add', ['CaisseCommercialController', 'add']);
$route->addRoute('/caisse_commercial/edit', ['CaisseCommercialController', 'edit']);
$route->addRoute('/caisse_commercial/changer', ['CaisseCommercialController', 'changer']);
$route->addRoute('/caisse_commercial/details/{param}', ['CaisseCommercialController', 'details']);
$route->addRoute('/caisse_commercial/edition/{param}', ['CaisseCommercialController', 'edition']);
$route->addRoute('/caisse_commercial/formulaire', ['CaisseCommercialController', 'formulaire']);

$route->addRoute('/type_depense/list', ['TypeDepenseController', 'list']);
$route->addRoute('/type_depense/apiList', ['TypeDepenseController', 'apiList']);
$route->addRoute('/type_depense/add', ['TypeDepenseController', 'add']);
$route->addRoute('/type_depense/edit', ['TypeDepenseController', 'edit']);
$route->addRoute('/type_depense/changer', ['TypeDepenseController', 'changer']);
$route->addRoute('/type_depense/details/{param}', ['TypeDepenseController', 'details']);
$route->addRoute('/type_depense/edition/{param}', ['TypeDepenseController', 'edition']);
$route->addRoute('/type_depense/formulaire', ['TypeDepenseController', 'formulaire']);

$route->addRoute('/depense/list', ['DepenseController', 'list']);
$route->addRoute('/depense/apiList', ['DepenseController', 'apiList']);
$route->addRoute('/depense/add', ['DepenseController', 'add']);
$route->addRoute('/depense/edit', ['DepenseController', 'edit']);
$route->addRoute('/depense/changer', ['DepenseController', 'changer']);
$route->addRoute('/depense/details/{param}', ['DepenseController', 'details']);
$route->addRoute('/depense/edition/{param}', ['DepenseController', 'edition']);
$route->addRoute('/depense/formulaire', ['DepenseController', 'formulaire']);

$route->addRoute('/versement/list', ['VersementController', 'list']);
$route->addRoute('/versement/apiList', ['VersementController', 'apiList']);
$route->addRoute('/versement/add', ['VersementController', 'add']);
$route->addRoute('/versement/edit', ['VersementController', 'edit']);
$route->addRoute('/versement/changer', ['VersementController', 'changer']);
$route->addRoute('/versement/details/{param}', ['VersementController', 'details']);
$route->addRoute('/versement/edition/{param}', ['VersementController', 'edition']);
$route->addRoute('/versement/formulaire', ['VersementController', 'formulaire']);

// -------------------------------------------------------------
// Module: Rôles, Permissions & Notifications
// -------------------------------------------------------------
$route->addRoute('/role/list', ['RoleController', 'list']);
$route->addRoute('/role/apiList', ['RoleController', 'apiList']);
$route->addRoute('/role/add', ['RoleController', 'add']);
$route->addRoute('/role/edit', ['RoleController', 'edit']);
$route->addRoute('/role/changer', ['RoleController', 'changer']);
$route->addRoute('/role/details/{param}', ['RoleController', 'details']);
$route->addRoute('/role/edition/{param}', ['RoleController', 'edition']);
$route->addRoute('/role/formulaire', ['RoleController', 'formulaire']);

$route->addRoute('/permission/list', ['PermissionController', 'list']);
$route->addRoute('/permission/apiList', ['PermissionController', 'apiList']);
$route->addRoute('/permission/add', ['PermissionController', 'add']);
$route->addRoute('/permission/edit', ['PermissionController', 'edit']);
$route->addRoute('/permission/changer', ['PermissionController', 'changer']);
$route->addRoute('/permission/details/{param}', ['PermissionController', 'details']);
$route->addRoute('/permission/edition/{param}', ['PermissionController', 'edition']);
$route->addRoute('/permission/formulaire', ['PermissionController', 'formulaire']);
$route->addRoute('/permission/addModule', ['PermissionController', 'addModule']);

$route->addRoute('/notification/list', ['NotificationController', 'list']);
$route->addRoute('/notification/apiList', ['NotificationController', 'apiList']);
$route->addRoute('/notification/add', ['NotificationController', 'add']);
$route->addRoute('/notification/edit', ['NotificationController', 'edit']);
$route->addRoute('/notification/changer', ['NotificationController', 'changer']);
$route->addRoute('/notification/details/{param}', ['NotificationController', 'details']);
$route->addRoute('/notification/edition/{param}', ['NotificationController', 'edition']);
$route->addRoute('/notification/formulaire', ['NotificationController', 'formulaire']);

// -------------------------------------------------------------
// Extraction & Exécution de l'URL
// -------------------------------------------------------------
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strpos($url, '/ovolias/public') === 0) {
    $url = str_replace('/ovolias/public', '', $url);
} elseif (strpos($url, '/ovolias') === 0) {
    $url = str_replace('/ovolias', '', $url);
} elseif (strpos($url, '/geicg/public') === 0) {
    $url = str_replace('/geicg/public', '', $url);
} elseif (strpos($url, '/geicg') === 0) {
    $url = str_replace('/geicg', '', $url);
}

$url = rtrim($url, '/');
if ($url === '') {
    $url = '/';
}
$route->run($url);

