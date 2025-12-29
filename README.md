# 🛒 Plateforme E-commerce Montres 

## 📊 Base de données - watch_store

### Choix du schéma 
- **Une seule table `users`** avec colonne `role ENUM('user', 'admin')` → respect strict de l'énoncé ("une table users" + "gestion des rôles").
- Table `items` → produits (montres).
- Table `cart` → panier.
- **Bonus avancés** :
  - Système complet de commandes (`commandes` + `commande_items`).
  - Gestion stock (`stock_quantity` dans items).
  - Multi-images (img1, img2, img3) pour meilleure UX montres.
  - Vue `vue_commandes_completes` pour admin.

### 🗄️ Schéma des tables principales

#### users (clients + admins)
| Champ     | Type                  | Description                       |
|-----------|-----------------------|-----------------------------------|
| id        | INT PK AI            | Identifiant                       |
| name      | VARCHAR(100) UNIQUE  | Username                          |
| password  | VARCHAR(255)         | À hasher en PHP !                 |
| role      | ENUM('user','admin') | Rôle (gestion des accès)          |
| email     | VARCHAR(150) UNIQUE  | Email                             |
| phone     | VARCHAR(20)          | Téléphone (clients)               |
| address   | TEXT                 | Adresse livraison (clients)       |

#### items (montres)
| Champ           | Type              | Description                          |
|-----------------|-------------------|--------------------------------------|
| id              | INT PK AI        | Identifiant                          |
| name            | VARCHAR(200)     | Nom de la montre                     |
| description     | TEXT             | Description                          |
| prix            | DECIMAL(10,2)    | Prix en DZD                          |
| img1 / img2 / img3 | VARCHAR(255)  | 3 images par montre (face, bracelet, détail) |
| categorie       | VARCHAR(100)     | Luxury, Sport, Dress, Casual, Smart  |
| stock_quantity  | INT              | Stock disponible                     |

#### cart
| Champ      | Type              | Description                          |
|------------|-------------------|--------------------------------------|
| user_id    | INT FK           | Référence users.id                   |
| item_id    | INT FK           | Référence items.id                   |
| quantite   | INT              | Quantité                             |
| prix       | DECIMAL(10,2)    | Prix au moment de l'ajout            |
| img        | VARCHAR(255)     | Image principale                     |

#### commandes & commande_items
Système complet pour finaliser les achats (status, livraison, détails).

### 🔐 Comptes test
- **Admin** : `admin` / `admin123` → accès admin
- **Clients** : `client_test` / `client123` et `alice_dz` / `alice123`

### 📸 Images
Dossier `/imgs/` → mets les photos comme `rolex_submariner.jpg`, `_2.jpg`, `_3.jpg` etc.

