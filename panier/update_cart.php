<?php
session_start();
include('../config.php');

// Vérification de la connexion utilisateur
if (!isset($_SESSION['id'])) {
    header("Location: Client/index.php?error=Veuillez vous connecter");
    exit();
}

$user_id = $_SESSION['id'];

// Vérification de la quantité et de l'ID du produit
if (isset($_POST['quantite']) && isset($_POST['produit_id'])) {
    $quantite = (int) $_POST['quantite'];
    $produit_id = (int) $_POST['produit_id'];

    // Vérifier que la quantité est supérieure à 0
    if ($quantite > 0) {
        // Mise à jour de la quantité dans la table panier
        $stmt = $con->prepare("UPDATE panier SET quantite = ? WHERE client_id = ? AND produit_id = ?");
        $stmt->bind_param("iii", $quantite, $user_id, $produit_id);
        $stmt->execute();

        // Redirection vers le panier avec un message de succès
        header("Location: view_cart.php?success=Quantité mise à jour");
        exit();
    } else {
        // Redirection avec un message d'erreur si la quantité est invalide
        header("Location: panier.php?error=Quantité invalide");
        exit();
    }
} else {
    // Redirection avec un message d'erreur si des paramètres sont manquants
    header("Location: panier.php?error=Paramètres manquants");
    exit();
}
?>
