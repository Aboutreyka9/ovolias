le projet initial est une application de gestion d'ecole EICG, nous avons aimer sa structure etses interfaces, sa gestion des roles et permission et son design. nous allons progressivement l'adapter a notre projet olive service.olive service est une structure qui permet au client de souscrire a des packs et de cotiseren fonction de leur budge grace aux commerciaux qui collecte de façon quotidien ou non, ils peuvent décider de payer pour plusieurs jour d'affilée. et une fois la session terminer et que le client ai fini de solder ses souscription. il récupère ses article des pack lors de la distribution. le commercial fait le versement au près des agents de gestion(comptable,administrateur,gestionnaire).

Enregistrement d'un pack.
nous allons la décomposer en 3 etape : 
etape 1 : Designation & Tarification du pack
les champs concerné (Désignation / Nom du Pack, Cotisation / Jour (FCFA), Photo / Visuel du Pack(optionnel));
etape 2 : les autres composant du pack
les champs concerné (Libelle session  en select, Libelle Categorie  en select, Nombre de jour  en readonly suite a la selection de session, Montant total en readonly calculer en fonction de champs Nombre de jour  et de Cotisation / Jour.)
etape 3 : SELECTION DES ARTICLES DU PACK ( count du nombre de choix de packs)
champs concerné : 
select liste des articles en select2,
bouton pour ajouter article selected dans le tableau,
tableau qui contient liste des article selection avec entete (Libelle article,Quantite, Action (remove on table article))



Processus de souscription en 3 étapes.

1. Informations personnelles du client

Cette première étape permet d'identifier le client et d'enregistrer ses informations personnelles. Renseignez les informations obligatoires telles que le nom complet, le contact, le genre et le lieu de résidence. L'adresse e-mail et la profession peuvent également être renseignées afin de compléter le profil du client mais optionnel.

2. Sélection des packs

Cette étape permet au client de choisir le ou les packs auxquels il souhaite souscrire. Sélectionnez d'abord la session, puis consultez les packs disponibles par catégorie ou non. Chaque pack présente son libelle pack, son annee d'activité, sa photo dans card, son libelle de categorie comme badge avec differente couleur (mais identique pour les packs de meme categorie), son montant, son nombre total d'articles et sa durée en jours et le nombre de fois deja contenu dans les souscription. Le client peut sélectionner plusieurs packs selon ses besoins et la selection se fais par clique sur le card et celui ci est aussi tot marquer comme selection en changeant de colleur automatiquement vis versa.

3. Récapitulatif et validation

La dernière étape présente un récapitulatif de la souscription avant sa validation. 
card avec Les informations personnelles du client. un tableau de liste des packs sélectionnés  affichés avec leurs montants respectifs et un bouton pour retirer de la liste. Le montant total de la souscription est automatiquement calculé afin de permettre au client ou à l'agent de vérifier les informations avant de confirmer définitivement la souscription.

