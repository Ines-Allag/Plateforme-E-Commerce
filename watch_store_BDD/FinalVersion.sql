-- ========================================
-- BASE DE DONNÉES: watch_store
-- ========================================
-- Supprimer la base si elle existe déjà
DROP DATABASE IF EXISTS watch_store;
-- Créer la base de données
CREATE DATABASE watch_store
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
USE watch_store;

-- ========================================
-- Table 1: utilisateurs (clients + admins)
-- ========================================
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('client', 'admin') NOT NULL DEFAULT 'client',
    email VARCHAR(150),
    telephone VARCHAR(20),
    adresse TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   
    INDEX idx_role (role),
    INDEX idx_nom (nom_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Table 2: produits (montres)
-- ========================================
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
   
    -- 3 images
    image1 VARCHAR(255),
    image2 VARCHAR(255),
    image3 VARCHAR(255),
   
    categorie VARCHAR(100),
    quantite_stock INT DEFAULT 0,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   
    INDEX idx_categorie (categorie),
    INDEX idx_prix (prix)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Table 3: panier
-- ========================================
CREATE TABLE panier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    prix DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE,
    UNIQUE KEY panier_unique (utilisateur_id, produit_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Table 4: commandes
-- ========================================
CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    statut ENUM('en_attente', 'confirmee', 'expediee', 'livree', 'annulee') DEFAULT 'en_attente',
   
    -- Informations de livraison
    nom_livraison VARCHAR(200) NOT NULL,
    adresse_livraison TEXT NOT NULL,
    telephone_livraison VARCHAR(20) NOT NULL,
    email_livraison VARCHAR(150),
   
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
   
    INDEX idx_statut (statut),
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_date (date_creation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Table 5: details_commande
-- ========================================
CREATE TABLE details_commande (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    nom_produit VARCHAR(200) NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
   
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE,
   
    INDEX idx_commande (commande_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- DONNÉES DE TEST
-- ========================================
-- Montres avec 3 images chacune (aucun changement)
INSERT INTO produits (nom, description, prix, image1, image2, image3, categorie, quantite_stock) VALUES
('Rolex Submariner', 'Montre de plongée légendaire avec mouvement automatique, étanche jusqu\'à 300m. Cadran noir emblématique.',
 8500.00, 'imgs/rolex_submariner.jpg', 'imgs/rolex_submariner_2.jpg', 'imgs/rolex_submariner_3.jpg', 'Luxury', 5),
('Omega Speedmaster', 'La montre lunaire, chronographe emblématique avec mouvement manuel. Première montre portée sur la Lune.',
 6200.00, 'imgs/omega_speedmaster.jpg', 'imgs/omega_speedmaster_2.jpg', 'imgs/omega_speedmaster_3.jpg', 'Luxury', 8),
('TAG Heuer Carrera', 'Chronographe sportif élégant avec mouvement automatique. Design inspiré des courses automobiles.',
 4800.00, 'imgs/tag_carrera.jpg', 'imgs/tag_carrera_2.jpg', 'imgs/tag_carrera_3.jpg', 'Sport', 12),
('Seiko 5 Sports', 'Montre automatique fiable et abordable, idéale pour tous les jours. Excellent rapport qualité-prix.',
 350.00, 'imgs/seiko_5.jpg', 'imgs/seiko_5_2.jpg', 'imgs/seiko_5_3.jpg', 'Casual', 25),
('Casio G-Shock', 'Montre ultra-résistante aux chocs avec fonctions digitales. Parfaite pour les sports extrêmes.',
 180.00, 'imgs/casio_gshock.jpg', 'imgs/casio_gshock_2.jpg', 'imgs/casio_gshock_3.jpg', 'Sport', 40),
('Apple Watch Series 9', 'Smartwatch avec GPS, suivi santé et notifications. Écran always-on et batterie longue durée.',
 450.00, 'imgs/apple_watch.jpg', 'imgs/apple_watch_2.jpg', 'imgs/apple_watch_3.jpg', 'Smart', 30),
('Tissot PRX Powermatic', 'Montre vintage inspirée des années 70, mouvement automatique avec 80h de réserve de marche.',
 750.00, 'imgs/tissot_prx.jpg', 'imgs/tissot_prx_2.jpg', 'imgs/tissot_prx_3.jpg', 'Dress', 15),
('Orient Bambino', 'Montre classique élégante avec cadran dressé et mouvement automatique. Parfaite pour occasions formelles.',
 280.00, 'imgs/orient_bambino.jpg', 'imgs/orient_bambino_2.jpg', 'imgs/orient_bambino_3.jpg', 'Dress', 20),
('Citizen Eco-Drive', 'Montre solaire qui ne nécessite jamais de changement de pile. Technologie Eco-Drive exclusive.',
 320.00, 'imgs/citizen_ecodrive.jpg', 'imgs/citizen_ecodrive_2.jpg', 'imgs/citizen_ecodrive_3.jpg', 'Casual', 18),
('Hamilton Khaki Field', 'Montre militaire inspirée des montres de terrain américaines. Robuste et lisible avec style vintage.',
 550.00, 'imgs/hamilton_khaki.jpg', 'imgs/hamilton_khaki_2.jpg', 'imgs/hamilton_khaki_3.jpg', 'Sport', 10);

-- Commande exemple (utilisateur_id=2 = client_test)
INSERT INTO commandes (utilisateur_id, total, statut, nom_livraison, adresse_livraison, telephone_livraison, email_livraison) VALUES
(2, 8950.00, 'en_attente', 'Client Test', '15 Rue Didouche Mourad, Alger 16000', '+213555123456', 'client@test.com');

-- Détails de la commande exemple
INSERT INTO details_commande (commande_id, produit_id, nom_produit, quantite, prix_unitaire) VALUES
(1, 1, 'Rolex Submariner', 1, 8500.00),
(1, 6, 'Apple Watch Series 9', 1, 450.00);

-- ========================================
-- VUE UTILE: vue_commandes_completes
-- ========================================
CREATE VIEW vue_commandes_completes AS
SELECT
    c.id AS commande_id,
    c.utilisateur_id,
    u.nom_utilisateur,
    u.email,
    c.total,
    c.statut,
    c.nom_livraison,
    c.adresse_livraison,
    c.telephone_livraison,
    c.date_creation,
    COUNT(d.id) AS nombre_articles
FROM commandes c
JOIN utilisateurs u ON c.utilisateur_id = u.id
LEFT JOIN details_commande d ON c.id = d.commande_id
GROUP BY c.id;