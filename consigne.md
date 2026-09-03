 avant de generer une quelconque interface ou code,  verifie toujour dans le fichier database/olive.sql  a fin de faire une corespondance parfaite des informations et de produire quelque chose de coherent.
 avant de faire une modification dans la base de données, explique moi les raison de cette modification claire.

---

# FONCTIONNALITÉ DE PAIEMENT DES CAUTISATIONS

## 📋 Vue d'ensemble

Implémentation complète d'un système de gestion des paiements de cautisations pour les clients/étudiants. Le processus comporte 3 étapes majeures : recherche de souscription, affichage de la situation, et formulaire de paiement.

### Tables de base de données impliquées

- `souscriptions` : code_souscription, client_code, zone_code, session_code, annee_code, statut_souscription
- `clients` : code_client, nom_client, telephone_client, sexe_client, lieu_residence_client, profession_client, email_client
- `packs` : code_pack, libelle_pack, prix_cotisation_pack, session_code
- `pack_souscriptions` : souscription_code, pack_code (jointure n:m)
- `cautisation_clients` : code_cautisation_client, montant_cautisation_client, nombre_jour, souscription_code, mode_paiement, created_at_cautisation_client

---

## 🔍 ÉTAPE 1 : RECHERCHE D'UNE SOUSCRIPTION

### Critères de recherche

L'utilisateur peut rechercher par:
- **Numéro de téléphone** du client (telephone_client)
- **Nom du client** (nom_client) - recherche partiellement (LIKE)
- **Code client** (code_client) - correspondance exacte
- **Code souscription** (code_souscription) - correspondance exacte

### Résultats affichés

Tableau avec colonnes:
- Nom du client
- Code souscription
- Libellé de la session (depuis sessions.libelle_session)
- Montant total des packs (somme des prix_cotisation_pack pour cette souscription)
- Actions (bouton Sélectionner)

### Règles métier

- Afficher uniquement les souscriptions avec statut 'valide'
- Les résultats doivent être filtrés par établissement_code de l'utilisateur connecté

---

## 💳 ÉTAPE 2 : AFFICHAGE DE LA SITUATION

### Section 1 : Informations du client (Card)

Afficher une card avec titre "Informations du Client"

Données du client:
- **Nom complet** : `nom_client` + `prenom_client`
- **Contact** : `telephone_client`
- **Genre** : `sexe_client`
- **Lieu de résidence** : `lieu_residence_client`
- **Code client** : `code_client`
- **Email** : `email_client`
- **Profession** : `profession_client`

### Section 2 : Historique des cautisations (Card)

Card-header avec:
- Titre : "Liste des cautisations"
- Bouton : "Faire paiement" (classe btn-primary)

Tableau avec colonnes:
- Date de paiement (created_at_cautisation_client)
- Montant versé (montant_cautisation_client)
- Nombre de jours payés (nombre_jour)
- Mode de paiement (mode_paiement)
- Statut (statut_cautisation_client)

Calculs affichés sous le tableau:
- **Montant total déjà payé** : SUM(montant_cautisation_client)
- **Nombre total de jours payés** : SUM(nombre_jour)
- **Montant total de la cautisation** : SUM(prix_cotisation_pack) pour tous les packs de la souscription
- **Montant restant à payer** : Montant total - Montant déjà payé
- **Nombre total de jours** : depuis sessions.nombre_jour_session
- **Nombre de jours restant à payer** : Nombre total de jours - Nombre de jours déjà payés

---

## 🧾 ÉTAPE 3 : FORMULAIRE DE PAIEMENT (MODAL)

Le modal est divisé en deux sections avec un titre "Paiement de Cautisation".

### Partie supérieure : Récapitulatif (Non-éditable)

Afficher sous forme de lignes clé-valeur:

```
Client : [nom_client]
Code Souscription : [code_souscription]
Session : [libelle_session]
Montant total de la cautisation : [FCFA]
Montant déjà payé : [FCFA]
Montant restant à payer : [FCFA]
Nombre total de jours : [jours]
Nombre de jours déjà payés : [jours]
Nombre de jours restant à payer : [jours]
```

### Partie inférieure : Formulaire (Éditable)

#### Champ 1 : Montant de la cautisation

- **Type** : Champ text, readonly (affichage uniquement)
- **Valeur** : SUM(prix_cotisation_pack) pour tous les packs liés à la souscription
- **Format** : Nombre avec séparateurs (ex: 10 000 FCFA)
- **Étiquette** : "Montant de la cautisation journalière"

#### Champ 2 : Mode de paiement

- **Type** : Select/Dropdown
- **Options disponibles** :
  - Espèces
  - Mobile Money
  - Autres modes (si configurés dans les settings)
- **Requis** : Oui
- **Défaut** : Espèces

#### Champ 3 : Type de paiement

- **Type** : Radio buttons ou Toggle
- **Options** :
  - "Saisir le montant"
  - "Saisir le nombre de jours"
- **Défaut** : "Saisir le montant"
- **Comportement** : Au changement, masquer/afficher les champs correspondants

#### Champ 4 : Montant à verser

- **Type** : Champ number
- **Visible si** : Type de paiement = "Saisir le montant"
- **Requis** : Oui (si visible)
- **Validation** :
  - Doit être un multiple du prix_cotisation_pack
  - Doit être > 0
  - Doit être ≤ montant restant à payer
- **Événement** : À chaque changement, calculer automatiquement le nombre de jours

#### Champ 5 : Nombre de jours

- **Type** : Champ number
- **Visible si** : Type de paiement = "Saisir le nombre de jours"
- **Requis** : Oui (si visible)
- **Validation** :
  - Doit être > 0
  - Doit être ≤ nombre de jours restant à payer
- **Événement** : À chaque changement, calculer automatiquement le montant

#### Champ 6 : Date du prochain rendez-vous

- **Type** : Champ date, readonly (affichage uniquement)
- **Calcul** : Date de paiement + Nombre de jours payés
- **Format** : JJ/MM/YYYY

#### Champ 7 : Bouton de validation

- **Type** : Bouton primary
- **Texte** : "Valider le paiement"
- **État** : Désactivé si le formulaire n'est pas valide
- **Action** : Enregistrer dans cautisation_clients et fermer le modal

---

## 🧮 RÈGLES DE CALCUL IMPORTANTES

### Calcul montant ↔ nombre de jours

**Formule** :
```
montant = nombre_de_jours × prix_cotisation_pack
nombre_de_jours = montant ÷ prix_cotisation_pack
```

**Exemple** : Si prix_cotisation_pack = 100 FCFA/jour
- 1 jour = 100 FCFA
- 5 jours = 500 FCFA
- 10 jours = 1000 FCFA
- 20 jours = 2000 FCFA

### Validation du montant

Le montant versé DOIT être un multiple exact du prix_cotisation_pack.

**Montants valides** (avec prix_cotisation_pack = 100) :
- 100, 200, 300, 500, 1000, 2000...

**Montants INVALIDES** :
- 150, 250, 550, 1500...

**Implémentation** :
```
Si (montant % prix_cotisation_pack) !== 0 → Erreur
Message : "Le montant doit être un multiple de [prix_cotisation_pack] FCFA"
```

### Calcul du prochain rendez-vous

**Formule** :
```
prochain_rendez_vous = date_paiement + nombre_de_jours_payés
```

**Exemple** :
- Date de paiement : 01/09/2026
- Nombre de jours payés : 5
- Prochain rendez-vous : 06/09/2026

**Implémentation** :
```javascript
const nextDate = new Date(paymentDate);
nextDate.setDate(nextDate.getDate() + numberOfDays);
```

---

## 🔒 Contraintes et validations

### Avant affichage du formulaire

- ✓ Vérifier que la souscription existe et a le statut 'valide'
- ✓ Vérifier que le client existe
- ✓ Vérifier que la session existe
- ✓ Vérifier que le montant restant à payer > 0
- ✓ Vérifier les permissions de l'utilisateur

### Dans le formulaire

- ✓ Montant à verser > 0
- ✓ Montant à verser ≤ montant restant à payer
- ✓ Montant doit être un multiple du prix_cotisation_pack
- ✓ Nombre de jours > 0
- ✓ Nombre de jours ≤ nombre de jours restant à payer
- ✓ Mode de paiement sélectionné
- ✓ Type de paiement sélectionné

### Après enregistrement

- ✓ Insérer une nouvelle ligne dans cautisation_clients
- ✓ Fermer le modal
- ✓ Rafraîchir le tableau de l'historique
- ✓ Afficher un message de succès
- ✓ Recalculer les totaux affichés

---

## 📊 Structure des données

### cautisation_clients (table d'enregistrement des paiements)

```sql
INSERT INTO cautisation_clients (
  code_cautisation_client,
  montant_cautisation_client,
  nombre_jour,
  souscription_code,
  statut_cautisation_client,
  mode_paiement,
  created_at_cautisation_client,
  updated_at_cautisation_client,
  etablissement_code,
  user_code,
  annee_code,
  zone_code,
  caisse_code
) VALUES (...)
```

Champs à générer:
- `code_cautisation_client` : Code unique (ex: CAUT-[timestamp]-[random])
- `montant_cautisation_client` : Montant saisi par l'agent
- `nombre_jour` : Nombre de jours calculés
- `souscription_code` : Code de la souscription sélectionnée
- `statut_cautisation_client` : 'valide' (par défaut)
- `mode_paiement` : Mode sélectionné (Espèces, Mobile Money, etc.)
- `created_at_cautisation_client` : NOW()
- `updated_at_cautisation_client` : NOW()
- `etablissement_code` : De la session utilisateur
- `user_code` : De la session utilisateur
- `annee_code` : De la souscription
- `zone_code` : De la souscription
- `caisse_code` : À récupérer depuis les settings ou la caisse ouverte

---

## 🎯 Actions et permissions requises

L'utilisateur doit avoir la permission :
- `MANAGE_PAYMENTS` (Gestion de la Caisse et Encaissements) - FINANCE

Rôles autorisés:
- ROLE_CAISSIER (Agent de Caisse)
- ROLE_COMPTABLE (Chef Comptable)
- ROLE_SUPERADMIN

---

## 📁 Fichiers à créer/modifier

### Modèles
- `models/Souscription.php` (vérifier/compléter)
- `models/Client.php` (vérifier/compléter)
- `models/Pack.php` (vérifier/compléter)
- `models/CautisationClient.php` (créer)

### Contrôleurs
- `controllers/souscriptions/ControllerSouscription.php` (créer ou modifier)
- `controllers/cautisations/ControllerCautisation.php` (créer)

### Vues
- `views/cautisations/search.php` (formulaire de recherche)
- `views/cautisations/situation.php` (affichage situation)
- `views/cautisations/payment_modal.php` (modal de paiement)

### Routes
- GET /cautisations/search → formulaire de recherche
- POST /cautisations/search → liste des souscriptions
- GET /cautisations/:code_souscription → affichage situation
- POST /cautisations/payment → enregistrement du paiement

---

## 🔑 AUTHENTIFICATION & ACCÈS COMPTES

### Comptes Administrateur par défaut

- **Identifiant** : `admin@gmail.com` ou `0544564564`
- **Mot de passe par défaut** : `admin`

### Règles d'authentification

- **Recherche d'identifiant** : Acceptation flexible de l'adresse email ou du numéro de téléphone (support du format international avec ou sans indicatif `+225`, nettoyé via `Validator::cleanPhone`).
- **Hachage du mot de passe** : Hachage standard PHP `password_hash` (`PASSWORD_DEFAULT`). Fallback avec auto-mise à jour en hash sécurisé dans la base de données si un mot de passe texte hérité est saisi.