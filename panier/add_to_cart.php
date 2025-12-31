<?php
session_start();
include('../config.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: ../Client/index.php?error=Veuillez vous connecter");
    exit();
}

// Vérification des données du formulaire
if (isset($_POST['produit_id']) && isset($_POST['quantite']) && isset($_POST['prix'])) {
    $produit_id = intval($_POST['produit_id']);
    $quantite = intval($_POST['quantite']);
    $prix = floatval($_POST['prix']);
    $user_id = intval($_SESSION['id']);

    // Vérifier que la quantité est valide
    if ($quantite < 1) {
        header("Location: ../index1.php?error=Quantité invalide");
        exit();
    }

    // Récupérer l'image correspondante depuis la table `produits`
    $query_image = "SELECT image1 FROM produits WHERE id = $produit_id";
    $result_image = mysqli_query($con, $query_image);

    if ($row_image = mysqli_fetch_assoc($result_image)) {
        $img = $row_image['image1'];
    } else {
        header("Location: ../index1.php?error=Produit introuvable");
        exit();
    }

    // Ajouter ou mettre à jour le produit dans la table `panier`
    $check_query = "SELECT * FROM panier WHERE utilisateur_id = $user_id AND produit_id = $produit_id";
    $result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Si le produit existe déjà dans le panier, mettre à jour la quantité
        $update_query = "UPDATE panier 
                         SET quantite = quantite + $quantite 
                         WHERE utilisateur_id = $user_id AND produit_id = $produit_id";
        mysqli_query($con, $update_query);
    } else {
        // Insérer un nouveau produit dans le panier avec l'image
        $insert_query = "INSERT INTO panier (utilisateur_id, produit_id, quantite, prix, image) 
                         VALUES ($user_id, $produit_id, $quantite, $prix, '$img')";
        mysqli_query($con, $insert_query);
    }

    // Redirection vers la page du panier
    header("Location: view_cart.php?success=Produit ajouté au panier");
    exit();
} else {
    // Redirection si les données du formulaire sont manquantes
    header("Location: ../index1.php?error=Données manquantes");
    exit();
}
?>