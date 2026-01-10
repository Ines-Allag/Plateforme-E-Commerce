<?php
session_start();
include('../config.php');

// Vérifier si l'user est connecté
if (!isset($_SESSION['id'])) {
    header("Location: ../Client/index.php?error=Veuillez vous connecter");
    exit();
}


 // Supprimer le produit du panier dans la base de données
if (isset($_POST['produit_id'])) {
    $produit_id = intval($_POST['produit_id']);
    $user_id = intval($_SESSION['id']);
    $delete_query = "DELETE FROM panier WHERE utilisateur_id = $user_id AND produit_id = $produit_id";
    mysqli_query($con, $delete_query);

    header("Location: view_cart.php?success=Produit supprimé");
    exit();
} else {
    header("Location: view_cart.php?error=Produit introuvable");
    exit();
}
?>