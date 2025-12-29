# 🛒 Plateforme E-commerce 

Mini-projet de conception d’une plateforme e-commerce spécialisée dans la vente de montres (luxe, sport, casual, smart).  

## 📊 Base de données – watch_store

### Choix du schéma 
- **Une seule table `utilisateurs`** avec colonne `role ENUM('client', 'admin')`.  
- Table `produits` → items (montres).  
- Table `panier` → cart.  
- Système complet de commandes avec `commandes` + `details_commande`.  
- Multi-images (image1, image2, image3) → meilleure expérience utilisateur pour les montres.  
- Gestion du stock (`quantite_stock`) → prêt pour éviter les ventes hors stock.

### 🗄️ Schéma des tables principales

| Table                  | Description                              |
|------------------------|------------------------------------------|
| **utilisateurs**       | Clients + Admins (rôles séparés)         |
| **produits**           | Catalogue des montres (avec 3 images)    |
| **panier**             | Panier d’achat par utilisateur           |
| **commandes**          | Commandes finalisées (livraison + statut)|
| **details_commande**   | Lignes de chaque commande                |

### Détails des tables

#### utilisateurs (clients + admins)
| Champ              | Type                  | Description                          |
|--------------------|-----------------------|--------------------------------------|
| id                 | INT PK AI            | Identifiant unique                   |
| nom_utilisateur    | VARCHAR(100) UNIQUE  | Nom d’utilisateur                    |
| mot_de_passe       | VARCHAR(255)         | Mot de passe (à hasher en PHP)       |
| role               | ENUM('client','admin')| Rôle de l’utilisateur                |
| email              | VARCHAR(150)         | Email                                |
| telephone          | VARCHAR(20)          | Téléphone (clients)                  |
| adresse            | TEXT                 | Adresse de livraison (clients)       |

#### produits (montres)
| Champ            | Type              | Description                              |
|------------------|-------------------|------------------------------------------|
| id               | INT PK AI        | Identifiant                              |
| nom              | VARCHAR(200)     | Nom de la montre                         |
| description      | TEXT             | Description détaillée                    |
| prix             | DECIMAL(10,2)    | Prix en DZD                              |
| image1 / image2 / image3 | VARCHAR(255) | 3 images par montre (face, bracelet, détail) |
| categorie        | VARCHAR(100)     | Luxury, Sport, Dress, Casual, Smart      |
| quantite_stock   | INT              | Stock disponible                         |

#### panier
| Champ           | Type              | Description                          |
|-----------------|-------------------|--------------------------------------|
| utilisateur_id  | INT FK           | Référence utilisateurs.id            |
| produit_id      | INT FK           | Référence produits.id                |
| quantite        | INT              | Quantité                             |
| prix            | DECIMAL(10,2)    | Prix au moment de l’ajout            |
| image           | VARCHAR(255)     | Image principale                     |

#### commandes & details_commande
Système complet pour finaliser les achats : statut (en_attente → livree), infos livraison, détails par produit.

→ Tables créées + 10 montres test + 1 commande exemple + vue utile.

### 🔐 Comptes de test
| Rôle   | Nom d’utilisateur | Mot de passe | Email                     |
|--------|-------------------|--------------|---------------------------|
| Admin  | admin             | admin123     | admin@watchstore.com      |
| Client | client_test       | client123    | client@test.com           |
| Client | alice_dz          | alice123     | alice@test.com            |

### 📸 Images
Toutes les photos sont dans le dossier `/imgs/`  
Exemple de nommage :  
`rolex_submariner.jpg` → `rolex_submariner_2.jpg` → `rolex_submariner_3.jpg`
