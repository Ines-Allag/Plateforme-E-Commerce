# 🛒 Plateforme E-commerce Montres

Mini-projet de conception d'une plateforme e-commerce spécialisée dans la vente de montres de luxe et casual.

## 📊 Base de données - watch_store (Version simple actuelle)

### Structure actuelle (4 tables - respect strict de l'énoncé)
- **clients** : Comptes des utilisateurs clients
- **admins** : Comptes des administrateurs (séparé pour sécurité renforcée)
- **produits** : Catalogue des montres avec **3 images par produit** (img1, img2, img3)
- **panier** : Gestion du panier d'achat par client

> Si le professeur autorise des tables supplémentaires → nous passerons à une version avancée avec :
> - Table `commandes` et `commande_items`
> - Champs spécifiques montres (brand, movement_type, stock_quantity, etc.)

### 🗄️ Schéma des tables

#### clients
| Champ     | Type              | Description                  |
|-----------|-------------------|------------------------------|
| id        | INT PK AI        | Identifiant unique           |
| name      | VARCHAR(100)     | Nom d'utilisateur (unique)   |
| password  | VARCHAR(255)     | Mot de passe (à hasher !)    |

#### admins
| Champ     | Type              | Description                  |
|-----------|-------------------|------------------------------|
| id        | INT PK AI        | Identifiant unique           |
| name      | VARCHAR(100)     | Nom d'utilisateur (unique)   |
| password  | VARCHAR(255)     | Mot de passe (à hasher !)    |

#### produits
| Champ       | Type              | Description                          |
|-------------|-------------------|--------------------------------------|
| id          | INT PK AI        | Identifiant unique                   |
| name        | VARCHAR(200)     | Nom de la montre                     |
| description | TEXT             | Description détaillée                |
| prix        | DECIMAL(10,2)    | Prix en DZD                          |
| img1        | VARCHAR(255)     | Image principale (face)              |
| img2        | VARCHAR(255)     | Image secondaire (bracelet/dos)      |
| img3        | VARCHAR(255)     | Image détail (cadran/mouvement)       |
| categorie   | VARCHAR(100)     | Luxury, Sport, Dress, Casual, Smart  |

#### panier
| Champ       | Type              | Description                          |
|-------------|-------------------|--------------------------------------|
| id          | INT PK AI        | Identifiant                          |
| client_id   | INT FK           | Référence clients.id                 |
| produit_id  | INT FK           | Référence produits.id                |
| quantite    | INT              | Quantité dans le panier              |
| prix        | DECIMAL(10,2)    | Prix au moment de l'ajout            |
| img         | VARCHAR(255)     | Image principale pour affichage      |

### 🚀 Comment installer la BDD
1. Ouvrir phpMyAdmin
2. Créer une nouvelle base `watch_store`
3. Aller dans l'onglet **Importer**
4. Sélectionner le fichier : `database/watch_store_simple.sql`
5. Cliquer sur **Exécuter**

→ Tout est créé + 10 montres de test avec images !

### 🔐 Comptes de test
- Admin : `admin` / `admin123` → accès DashboardAdmin.php
- Client : `client_test` / `client123` → accès boutique et panier

### 📸 Images
Toutes les images sont dans le dossier `/imgs/`  
Nomme-les comme dans la BDD :  
`rolex_submariner.jpg`, `rolex_submariner_2.jpg`, `rolex_submariner_3.jpg`, etc.
