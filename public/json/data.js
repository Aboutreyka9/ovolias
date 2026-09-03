// const fakeData = {
//     clients: [
//         { id: 11, code: 'CLI-6A220DA9', nom: 'Lespros Informatique', telephone: '0700000000', email: 'lespros1313@gmail.com', statut: 'actif' },
//         { id: 12, code: 'CLI-TEST1', nom: 'Test User', telephone: '0700000000', email: 'test@woli.com', statut: 'actif' },
//         { id: 1, code: 'CLI-001', nom: 'Amanda Konan', telephone: '0707070707', email: 'amanda@example.com', statut: 'actif' },
//         { id: 2, code: 'CLI-002', nom: 'Bamba Ouattara', telephone: '0707070708', email: 'bamba@example.com', statut: 'inactif' },
//         { id: 3, code: 'CLI-003', nom: 'Clarisse Kouadio', telephone: '0707070709', email: 'clarisse@example.com', statut: 'actif' }
//     ],
//     fournisseurs: [
//         { code: 'FOU-001', nom: 'Marché Adjamé Textile', telephone: '0700000001', localisation: 'Abidjan Adjamé', mode: 'dropshipping', statut: 'actif' },
//         { code: 'FOU-002', nom: 'Boutique Elegance', telephone: '0700000002', localisation: 'Cocody', mode: 'achat_direct', statut: 'actif' }
//     ],
//     categories: [
//         { code: 'CAT-001', libelle: 'Vêtements', statut: 'actif' },
//         { code: 'CAT-002', libelle: 'Pagne', statut: 'actif' },
//         { code: 'CAT-003', libelle: 'Sacs', statut: 'actif' },
//         { code: 'CAT-004', libelle: 'Chaussures', statut: 'actif' }
//     ],
//     sousCategories: [
//         { code: 'SCAT-001', categorie: 'CAT-001', libelle: 'T-shirts' },
//         { code: 'SCAT-002', categorie: 'CAT-001', libelle: 'Pantalons' },
//         { code: 'SCAT-003', categorie: 'CAT-002', libelle: 'Wax' },
//         { code: 'SCAT-004', categorie: 'CAT-003', libelle: 'Sacs à main' },
//         { code: 'SCAT-005', categorie: 'CAT-004', libelle: 'Sneakers' }
//     ],
//     produits: [
//         { code: 'PROD-001', fournisseur: 'FOU-001', sousCategorie: 'SCAT-001', libelle: 'T-shirt oversize noir', prixFournisseur: 2000, prixVente: 5000, stock: 50, stockStatut: 'disponible' },
//         { code: 'PROD-002', fournisseur: 'FOU-001', sousCategorie: 'SCAT-002', libelle: 'Pagne wax Ankara', prixFournisseur: 3000, prixVente: 8000, stock: 30, stockStatut: 'disponible' },
//         { code: 'PROD-003', fournisseur: 'FOU-002', sousCategorie: 'SCAT-003', libelle: 'Sac à main luxe', prixFournisseur: 5000, prixVente: 12000, stock: 20, stockStatut: 'faible' },
//         { code: 'PROD-004', fournisseur: 'FOU-002', sousCategorie: 'SCAT-004', libelle: 'Sneakers Air style', prixFournisseur: 7000, prixVente: 15000, stock: 15, stockStatut: 'faible' },
//         { code: 'PROD-005', fournisseur: 'FOU-001', sousCategorie: 'SCAT-005', libelle: 'Pagne Kente royal', prixFournisseur: 4000, prixVente: 10000, stock: 0, stockStatut: 'rupture' }
//     ],
//     commandes: [
//         { id: 1, code: 'CMD-001', client: 'CLI-TEST1', montant: 22000, statut: 'en_attente', methode: 'cash', date: '2026-06-05' },
//         { id: 256, code: 'CMD-256', client: 'CLI-6A220DA9', montant: 45000, statut: 'livree', methode: 'mobile_money', date: '2026-06-15' },
//         { id: 255, code: 'CMD-255', client: 'CLI-003', montant: 18500, statut: 'en_livraison', methode: 'carte', date: '2026-06-14' }
//     ],
//     livraisons: [
//         { code: 'LIV-001', commande: 'CMD-001', statut: 'en_attente', frais: 1500 },
//         { code: 'LIV-002', commande: 'CMD-256', statut: 'livree', frais: 2000 },
//         { code: 'LIV-003', commande: 'CMD-255', statut: 'en_cours', frais: 1500 }
//     ],
//     livreurs: [
//         { code: 'LIVR-001', nom: 'Konan Mamadou', telephone: '0700000003', moyen: 'moto', statut: 'actif' },
//         { code: 'LIVR-002', nom: 'Traore Issa', telephone: '0700000004', moyen: 'voiture', statut: 'actif' }
//     ],
//     paiements: [
//         { code: 'PAY-001', commande: 'CMD-001', methode: 'cash', montant: 22000, statut: 'en_attente' },
//         { code: 'PAY-002', commande: 'CMD-256', methode: 'mobile_money', montant: 45000, statut: 'paye' }
//     ],
//     mouvementsStock: [
//         { code: 'MV-001', produit: 'PROD-001', type: 'entree', quantite: 100, source: 'achat' },
//         { code: 'MV-002', produit: 'PROD-003', type: 'sortie', quantite: 5, source: 'commande' }
//     ],
//     villes: [
//         { code: 'ABJ', libelle: 'Abidjan' },
//         { code: 'YAM', libelle: 'Yamoussoukro' },
//         { code: 'BOU', libelle: 'Bouaké' }
//     ],
//     communes: [
//         { code: 'ABJ-COC', ville: 'ABJ', libelle: 'Cocody' },
//         { code: 'ABJ-PLA', ville: 'ABJ', libelle: 'Plateau' },
//         { code: 'YAM-Cen', ville: 'YAM', libelle: 'Centre-Ville' }
//     ],
//     favoris: [
//         { client: 'CLI-6A220DA9', produit: 'PROD-004' },
//         { client: 'CLI-6A220DA9', produit: 'PROD-001' },
//         { client: 'CLI-6A220DA9', produit: 'PROD-003' }
//     ],
//     users: [
//         { code: 'USR-001', nom: 'Admin Principal', email: 'admin@woli.com', statut: 'actif' },
//         { code: 'USR-002', nom: 'Manager Stock', email: 'stock@woli.com', statut: 'actif' }
//     ]
// };

// window.tableData = {
//     clients: fakeData.clients.map(function(c) {
//         return [c.code, c.nom, c.telephone, c.email, c.statut === 'actif' ? 'Actif' : 'Inactif', '<button class="btn-icon-sm"><i class="fas fa-eye"></i></button>'];
//     }),
//     fournisseurs: fakeData.fournisseurs.map(function(f) {
//         var modeMap = { 'dropshipping': 'Dropshipping', 'achat_direct': 'Achat direct' };
//         return [f.code, f.nom, f.telephone, f.localisation, modeMap[f.mode] || f.mode, '<span class="badge-status delivered">' + (f.statut === 'actif' ? 'Actif' : 'Inactif') + '</span>', '<button class="btn-icon-sm"><i class="fas fa-eye"></i></button>'];
//     }),
//     utilisateurs: fakeData.users.map(function(u) {
//         return [u.code, u.nom, u.email, 'Admin', '<span class="badge-status delivered">' + (u.statut === 'actif' ? 'Actif' : 'Inactif') + '</span>', '<button class="btn-icon-sm"><i class="fas fa-edit"></i></button>'];
//     }),
//     livraisons: fakeData.livraisons.map(function(l) {
//         var statusMap = { 'en_attente': 'En attente', 'en_cours': 'En cours', 'livree': 'Livrée', 'echec': 'Échec' };
//         var statusClass = l.statut === 'en_attente' ? 'pending' : l.statut === 'livree' ? 'delivered' : l.statut === 'en_cours' ? 'shipping' : 'cancelled';
//         return [l.code, l.commande, '-', l.frais.toLocaleString() + ' FCFA', '<span class="badge-status ' + statusClass + '">' + statusMap[l.statut] + '</span>', '<button class="btn-icon-sm"><i class="fas fa-eye"></i></button>'];
//     }),
//     produits: fakeData.produits.map(function(p) {
//         var cat = fakeData.sousCategories.find(function(s) { return s.code === p.sousCategorie; });
//         var fourn = fakeData.fournisseurs.find(function(f) { return f.code === p.fournisseur; });
//         var statusMap = { 'disponible': 'Disponible', 'faible': 'Faible', 'rupture': 'Rupture' };
//         return ['<strong>' + p.libelle + '</strong>', cat ? cat.libelle : '', fourn ? fourn.nom : '', p.prixFournisseur.toLocaleString() + ' FCFA', p.prixVente.toLocaleString() + ' FCFA', p.stock, '<span class="badge-status ' + (p.stockStatut === 'disponible' ? 'delivered' : p.stockStatut === 'faible' ? 'pending' : 'cancelled') + '">' + statusMap[p.stockStatut] + '</span>', '<button class="btn-icon-sm"><i class="fas fa-eye"></i></button>'];
//     }),
//     commandes: fakeData.commandes.map(function(c) {
//         var client = fakeData.clients.find(function(cl) { return cl.code === c.client; });
//         var statusMap = { 'en_attente': 'En attente', 'confirmee': 'Confirmée', 'en_livraison': 'En livraison', 'livree': 'Livrée', 'annulee': 'Annulée' };
//         var statusClass = c.statut === 'en_attente' ? 'pending' : c.statut === 'livree' ? 'delivered' : c.statut === 'en_livraison' ? 'shipping' : 'cancelled';
//         return [c.code, client ? client.nom : c.client, c.montant.toLocaleString() + ' FCFA', c.methode, '<span class="badge-status ' + statusClass + '">' + statusMap[c.statut] + '</span>', c.date, '<button class="btn-icon-sm"><i class="fas fa-eye"></i></button>'];
//     }),
//     paiements: fakeData.paiements.map(function(p) {
//         var statusMap = { 'en_attente': 'En attente', 'paye': 'Payé', 'echoue': 'Échoué' };
//         var statusClass = p.statut === 'en_attente' ? 'pending' : p.statut === 'paye' ? 'delivered' : 'cancelled';
//         return [p.code, p.commande, p.methode, p.montant.toLocaleString() + ' FCFA', '<span class="badge-status ' + statusClass + '">' + statusMap[p.statut] + '</span>'];
//     }),
//     mouvementsStock: fakeData.mouvementsStock.map(function(m) {
//         var produit = fakeData.produits.find(function(p) { return p.code === m.produit; });
//         var typeMap = { 'entree': 'Entrée', 'sortie': 'Sortie' };
//         var typeClass = m.type === 'entree' ? 'delivered' : 'cancelled';
//         return [m.code, produit ? produit.libelle : m.produit, '<span class="badge-status ' + typeClass + '">' + typeMap[m.type] + '</span>', m.quantite, m.source];
//     })
// };