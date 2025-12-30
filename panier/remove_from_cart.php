<?php
session_start();
include('../config.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['name']) || !isset($_SESSION['id'])) {
    header("Location: ../Client/index.php");
    exit();
}

if (isset($_POST['produit_id'])) {
    $produit_id = intval($_POST['produit_id']);
    $user_id = intval($_SESSION['id']); // ID de l'utilisateur connecté

    // Supprimer le produit du panier en session
    if (isset($_SESSION['cart'][$produit_id])) {
        unset($_SESSION['cart'][$produit_id]);
    }

    // Supprimer le produit du panier dans la base de données
    $delete_query = "DELETE FROM panier WHERE utilisateur_id = $user_id AND produit_id = $produit_id";
    mysqli_query($con, $delete_query);

    // Redirection vers la page du panier
    header("Location: view_cart.php");
    exit();
} else {
    header("Location: view_cart.php?error=Produit introuvable.");
    exit();
}
?>