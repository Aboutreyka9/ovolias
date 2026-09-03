# 🫒 Olive Service - Guide du Projet & Collaboration

Bienvenue dans le dépôt du projet **Olive Service**. Ce document constitue la référence centrale pour les développeurs et l'assistant IA. Il résume les règles de collaboration, l'architecture du projet, les spécifications métier, le système RBAC et l'état des modules.

---

## 📜 1. Règles Inviolables de Collaboration

> [!IMPORTANT]
> **Règle 1 : Alignement Base de Données (`database/olive.sql`)**  
> Avant de générer une quelconque interface ou un code PHP, vérifiez systématiquement les champs et relations dans `database/olive.sql` afin de garantir une correspondance parfaite des données.

> [!WARNING]
> **Règle 2 : Modifications de Base de Données**  
> Toute modification de structure SQL doit être expliquée clairement avec ses raisons métier avant d'être exécutée.

> [!NOTE]
> **Règle 3 : Architecture MVC Épurée**  
> Conservez la structure MVC stricte (`controllers/`, `models/`, `views/`, `core/`, `public/`). Aucun contrôleur ou modèle obsolète ne doit polluer le code actif.

---

## 💡 2. Présentation d'Olive Service

**Olive Service** est une plateforme de gestion commerciale et d'épargne/cotisation permettant aux clients de souscrire à des packs de produits (alimentaires, électroménager, événementiels...).

### 🔄 Flux métier principal :
1. **Souscription** : Le client s'inscrit et choisit un ou plusieurs packs de produits pour une session donnée.
2. **Cotisation quotidienne/flexible** : Les commerciaux collectent les cotisations sur le terrain (au jour le jour ou par versement groupé de plusieurs jours).
3. **Gestion de Trésorerie & Caisse** : Les commerciaux effectuent leurs versements auprès de la caisse (gestionnaires/comptables).
4. **Distribution** : Une fois la souscription entièrement soldée et la session clôturée, le client récupère les articles de son pack lors de la distribution.

---

## 🔑 3. Formules de Calcul Financier Officielles

- **Prix de cotisation journalière du pack** = `packs.prix_cotisation_pack`
- **Total Souscription** = $\sum(\text{prix\_cotisation\_pack}) \times \text{nombre\_jour\_session}$
- **Montant Total Payé** = $\sum(\text{montant\_cautisation\_client})$
- **Solde Restant à Payer** = $\text{Total Souscription} - \text{Montant Total Payé}$
- **Nombre de Jours Payés** = $\sum(\text{nombre\_jour})$
- **Nombre de Jours Restants** = $\text{nombre\_jour\_session} - \text{Nombre de Jours Payés}$

---

## 🛡️ 4. Système RBAC & 4 Profils Utilisateurs Officielles

| Profil | Code Rôle | Périmètre (Scope) & Droits Accordés | Restrictions Strictes |
|---|---|---|---|
| **Commercial Terrain** | `ROLE_COMMERCIAL` | Enregistrement des clients, création de souscriptions, collecte cotisations (statut `en_attente`), ouverture/clôture de sa propre caisse, versement. Scope limité à son `user_code`. | ❌ **Aucune modification/suppression** sur les clients, souscriptions ou cotisations. |
| **Gestionnaire Catalogue** | `ROLE_GESTIONNAIRE` | CRUD Catalogue Articles, Catégories, Packs, Sessions, Années. Consultation/édition des fiches clients & souscriptions, validation des distributions. Scope par `zone_code`, `etablissement_code`. | ❌ Pas de validation de versements ni de dépenses comptables. |
| **Responsable Finance** | `ROLE_FINANCE` | Suivi des cotisations, validation/rejet des versements commerciaux, gestion des dépenses, clôtures de caisse globale. Scope par `etablissement_code`, `annee_code`. | ❌ Pas de création directe de souscriptions terrain. |
| **Administrateur** | `ROLE_ADMIN` | Configuration système, gestion des utilisateurs, attribution dynamique des rôles/permissions, statistiques & rapports globaux. | 👁️ Accès superviseur complet. |

Le fichier SQL d'initialisation des habilitations est hébergé sous [`database/rbac.sql`](file:///var/www/html/geicg/database/rbac.sql).

---

## 📂 5. Structure des Modules Actifs

```
/var/www/html/geicg/
├── config/                  # Configuration (Database connection, constantes)
├── core/                    # Moteur MVC (Router, BaseController, BaseModel, Context, Validator)
├── controllers/             # Contrôleurs actifs par module métier
│   ├── home/                # Tableau de bord
│   ├── users/               # Gestion des utilisateurs
│   ├── annees/              # Années d'activité
│   ├── sessions/            # Sessions de cotisation
│   ├── zones/               # Zones géographiques
│   ├── categories_articles/ # Catégories d'articles
│   ├── articles/            # Articles produits
│   ├── categorie_packs/     # Catégories de packs
│   ├── packs/               # Packs & Offres
│   ├── clients/             # Fichier clients
│   ├── zone_commercials/    # Zones commerciales
│   ├── souscriptions/       # Souscriptions clients
│   ├── cotisations/         # Paiements cautisations & situation (CautisationPaymentController)
│   ├── distributions/       # Retraits & livraisons de packs
│   ├── ouvertures_caisse/   # Ouvertures de caisse
│   ├── clotures_caisse/     # Clôtures de caisse
│   ├── type_depenses/       # Catégories de dépenses
│   ├── depenses/            # Saisie des dépenses
│   ├── versements/          # Versements des commerciaux
│   ├── roles/ & permissions/# Habilitations & accès RBAC
│   └── notifications/       # Système de notifications
├── models/                  # Modèles de données PDO
├── views/                   # Interfaces utilisateur PHP / Bootstrap / DataTables / Lucide
├── database/
│   ├── olive.sql            # Schéma de référence SQL
│   └── rbac.sql             # Scripts des 4 rôles et permissions RBAC
└── public/                  # Point d'entrée principal (index.php, inc/ header/nav/sidebar)
```

---

## 🗄️ 6. Identifiants de Base de Données

- **Hôte** : `localhost` / `127.0.0.1`
- **Nom de la base** : `olive`
- **Utilisateur** : `admin`
- **Mot de passe** : `admin`
- **Charset** : `utf8mb4`

---

## 📌 7. État d'Avancement des Tâches (Roadmap)

- [x] Migration de la base de données vers `olive.sql`.
- [x] Nettoyage et suppression définitive des modules scolaires/pressing obsolètes (`trash/`).
- [x] Implémentation du module de paiement des cotisations (`cautisation-payment/situation`).
- [x] Mise à jour des calculs financiers dynamiques sur la liste des souscriptions (`views/souscriptions/list.php`).
- [x] Ajout du bouton d'accès direct "Situation" dans les souscriptions.
- [x] Colonne Montant Total (`prix_cotisation_pack * nombre_jour_session`) et regroupement par Année/Zone sur les packs (`views/packs/list.php`).
- [x] Implémentation complète du système RBAC à 4 profils (Commercial, Gestionnaire, Finance, Admin).
- [x] Isolation et filtrage par Scope (`user_code`, `zone_code`, `etablissement_code`) dans les contrôleurs et la sidebar.
- [ ] Tableaux de bord de suivi financier et commercial en temps réel.
