<?php
// ========================================
// Configuration Database
// ========================================

// Connexion à la base de données
$con = mysqli_connect(
    "127.0.0.1",      // Serveur (localhost)
    "root",           // Utilisateur MySQL
    "",               // Mot de passe (vide par défaut sur XAMPP)
    "watch_store"     // Nom de la base de données
);

// Vérifier la connexion
if (!$con) {
    die("Erreur de connexion à la base de données: " . mysqli_connect_error());
}

// Définir l'encodage UTF-8 pour supporter tous les caractères
mysqli_set_charset($con, "utf8mb4");

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>