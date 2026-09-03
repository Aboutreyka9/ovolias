<?php
// Définition portable du répertoire racine absolu du projet
define('ROOT', dirname(__DIR__));

$httpHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLocalEnvironment = (strpos($httpHost, 'localhost') !== false || strpos($httpHost, '127.0.0.1') !== false);

// Gestion dynamique de l'URL racine (priorité à la variable d'environnement APP_URL, sinon détection automatique)
if (!defined('RACINE')) {
    $envUrl = $_ENV['APP_URL'] ?? getenv('APP_URL');
    if (!empty($envUrl)) {
        define('RACINE', rtrim($envUrl, '/') . '/');
    } else {
        define('RACINE', $isLocalEnvironment ? 'http://localhost/ovolias/' : 'https://ovolias.app/');
    }
}

if (!defined('ONESIGNAL_APP_ID')) {
    define('ONESIGNAL_APP_ID', $_ENV['ONESIGNAL_APP_ID'] ?? getenv('ONESIGNAL_APP_ID') ?: '54d8db10-a446-4542-9b2c-2d49d1433d59');
}
if (!defined('ONESIGNAL_REST_API_KEY')) {
    define('ONESIGNAL_REST_API_KEY', $_ENV['ONESIGNAL_REST_API_KEY'] ?? getenv('ONESIGNAL_REST_API_KEY') ?: '');
}

define('LOGO', '<span class="fw-bold fs-4 text-success" style="letter-spacing: 1px;">OLIVE SERVICE</span>');
define('ICON', '<span class="fw-bold fs-4 text-success">O</span>');
define('TITLE', 'Olive Service - Administration & Souscriptions');

const USERS_AUTH = 'users_auth';

class TABLES
{
    // INSTITUTION, UTILISATEURS & ACCÈS (RBAC)
    public const ETABLISSEMENTS         = 'etablissements';
    public const ANNEES                 = 'annees';
    public const SESSIONS               = 'sessions';
    public const ZONES                  = 'zones';
    public const ZONE_COMMERCIALS       = 'zone_commercials';
    public const FONCTIONS              = 'fonctions';
    public const USERS                  = 'users';
    public const ROLES                  = 'roles';
    public const PERMISSIONS            = 'permissions';
    public const ROLE_PERMISSIONS       = 'role_permissions';
    public const USER_PERMISSIONS       = 'user_permissions';
    public const USER_ROLES             = 'user_roles';

    // CATALOGUE & PACKS
    public const ARTICLES               = 'articles';
    public const CATEGORIE_PACKS        = 'categorie_packs';
    public const PACKS                  = 'packs';
    public const PACK_ARTICLES          = 'pack_articles';

    // CLIENTS & SOUSCRIPTIONS / COTISATIONS
    public const CLIENTS                = 'clients';
    public const SOUSCRIPTIONS          = 'souscriptions';
    public const PACK_SOUSCRIPTIONS     = 'pack_souscriptions';
    public const CAUTISATION_CLIENTS    = 'cautisation_clients';
    public const DISTRIBUTIONS          = 'distributions';

    // FINANCES & EXPÉDITION / CHARGES
    public const TYPE_DEPENSES          = 'type_depenses';
    public const DEPENSES               = 'depenses';
    public const PAIEMENTS              = 'paiements';
    public const VERSEMENTS_COMMERCIAUX = 'versements_commerciaux';
}

class ROLES
{
    public const SUPER_ADMIN = 'ROLE-ADMIN';
    public const PRESSING    = 'ROLE-PRO';
    public const LIVREUR     = 'ROLE-LIV';
}

class STATUTS
{
    // Constantes de statuts génériques
    public const ACTIF      = 'actif';
    public const INACTIF    = 'inactif';
    public const VALIDE     = 'valide';
    public const ANNULE     = 'annule';
    public const EN_ATTENTE = 'En attente';

    // PRESSINGS
    public const PRESSINGS           = ['actif','inactif','suspendu'];

    // CATEGORIES ARTICLES
    public const CATEGORIES_ARTICLES = ['actif','inactif'];

    // ARTICLES PRESSINGS
    public const ARTICLES_PRESSINGS   = ['actif','inactif'];

    // TARIFS ARTICLES
    public const TARIFS_ARTICLES      = ['actif','inactif'];

    // SERVICES
    public const SERVICES             = ['actif','inactif'];

    // CLIENTS
    public const CLIENTS             = ['actif','inactif'];

    // UTILISATEURS & ACCÈS
    public const USERS               = ['actif','inactif'];
    public const ROLES               = ['actif','inactif'];
    public const PERMISSIONS         = ['actif','inactif'];
    public const ROLES_PERMISSIONS   = ['actif','inactif'];

    // OLIVE SERVICE - PRODUITS & PACKS
    public const ARTICLES            = ['actif','inactif'];
    public const CATEGORIE_PACKS     = ['actif','inactif'];
    public const PACKS               = ['actif','inactif'];

    // OLIVE SERVICE - SOUSCRIPTIONS & COTISATIONS
    public const SOUSCRIPTIONS       = ['valide','solde','annule','reconduite'];
    public const CAUTISATIONS        = ['En attente','valide','ennule'];
    public const DISTRIBUTIONS       = ['En attente','valide','ennule'];
    public const ZONES               = ['actif','inactif'];
    public const ZONE_COMMERCIALS    = ['actif','inactif'];

    // FINANCES
    public const PAIEMENTS           = ['valide','annule','en_attente'];
    public const DEPENSES            = ['actif','inactif'];
}
