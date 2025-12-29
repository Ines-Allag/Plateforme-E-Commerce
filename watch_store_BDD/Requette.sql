-- ========================================
-- BASE DE DONNÉES: watch_store
-- Version SIMPLE pour le mini-projet e-commerce montres
-- Tables séparées admins / clients (justifié : séparation claire des rôles)
-- Multi-images : img1 (principale), img2, img3
-- ========================================

DROP DATABASE IF EXISTS watch_store;
CREATE DATABASE IF NOT EXISTS watch_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE watch_store;

-- ========================================
-- Table 1: clients
-- ========================================
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Table 2: admins
-- ========================================
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Table 3: produits (montres) avec 3 images
-- ========================================
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    img1 VARCHAR(255),        -- Image principale
    img2 VARCHAR(255),        -- Image secondaire 1
    img3 VARCHAR(255),        -- Image secondaire 2
    categorie VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Table 4: panier
-- ========================================
CREATE TABLE panier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    prix DECIMAL(10, 2) NOT NULL,
    img VARCHAR(255),         -- On garde une seule image pour le panier (img1 suffit)
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (client_id, produit_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- DONNÉES DE TEST
-- ========================================

-- Admin par défaut (username: admin / password: admin123 → à hasher plus tard)
INSERT INTO admins (name, password) VALUES ('admin', 'admin123');

-- Client test
INSERT INTO clients (name, password) VALUES ('client_test', 'client123');

-- Montres avec 3 images (tu places les fichiers _2.jpg et _3.jpg dans imgs/)
INSERT INTO produits (name, description, prix, img1, img2, img3, categorie) VALUES
('Rolex Submariner', 'Montre de plongée légendaire avec mouvement automatique, étanche jusqu\'à 300m', 8500.00, 
 'imgs/rolex_submariner.jpg', 'imgs/rolex_submariner_2.jpg', 'imgs/rolex_submariner_3.jpg', 'Luxury'),

('Omega Speedmaster', 'La montre lunaire, chronographe emblématique avec mouvement manuel', 6200.00, 
 'imgs/omega_speedmaster.jpg', 'imgs/omega_speedmaster_2.jpg', 'imgs/omega_speedmaster_3.jpg', 'Luxury'),

('TAG Heuer Carrera', 'Chronographe sportif élégant avec mouvement automatique', 4800.00, 
 'imgs/tag_carrera.jpg', 'imgs/tag_carrera_2.jpg', 'imgs/tag_carrera_3.jpg', 'Sport'),

('Seiko 5 Sports', 'Montre automatique fiable et abordable, idéale pour tous les jours', 350.00, 
 'imgs/seiko_5.jpg', 'imgs/seiko_5_2.jpg', 'imgs/seiko_5_3.jpg', 'Casual'),

('Casio G-Shock', 'Montre ultra-résistante aux chocs avec fonctions digitales', 180.00, 
 'imgs/casio_gshock.jpg', 'imgs/casio_gshock_2.jpg', 'imgs/casio_gshock_3.jpg', 'Sport'),

('Apple Watch Series 9', 'Smartwatch avec GPS, suivi santé et notifications', 450.00, 
 'imgs/apple_watch.jpg', 'imgs/apple_watch_2.jpg', 'imgs/apple_watch_3.jpg', 'Smart'),

('Tissot PRX Powermatic', 'Montre vintage inspirée des années 70, mouvement automatique 80h', 750.00, 
 'imgs/tissot_prx.jpg', 'imgs/tissot_prx_2.jpg', 'imgs/tissot_prx_3.jpg', 'Dress'),

('Orient Bambino', 'Montre classique élégante avec cadran dressé et mouvement automatique', 280.00, 
 'imgs/orient_bambino.jpg', 'imgs/orient_bambino_2.jpg', 'imgs/orient_bambino_3.jpg', 'Dress'),

('Citizen Eco-Drive', 'Montre solaire qui ne nécessite jamais de changement de pile', 320.00, 
 'imgs/citizen_ecodrive.jpg', 'imgs/citizen_ecodrive_2.jpg', 'imgs/citizen_ecodrive_3.jpg', 'Casual'),

('Hamilton Khaki Field', 'Montre militaire inspirée des montres de terrain américaines', 550.00, 
 'imgs/hamilton_khaki.jpg', 'imgs/hamilton_khaki_2.jpg', 'imgs/hamilton_khaki_3.jpg', 'Sport');

-- ========================================
-- MESSAGE DE CONFIRMATION
-- ========================================
SELECT 'Base de données watch_store créée et remplie avec succès !' AS Status;
SELECT COUNT(*) AS 'Nombre de montres' FROM produits;