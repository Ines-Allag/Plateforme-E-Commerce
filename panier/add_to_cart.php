<?php
session_start();
include('../config.php');

// Vérifier si notre user est connecté
if (!isset($_SESSION['id'])) {
    header("Location: ../Client/index.php?error=Veuillez vous connecter");
    exit();
}

// Vérifier les données du form
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

    // Récupérer la 1ere l'image depuis la table produits
    $stmt_image = $con->prepare("SELECT image1 FROM produits WHERE id = ?");
    $stmt_image->bind_param("i", $produit_id);
    $stmt_image->execute();
    $result_image = $stmt_image->get_result();

    if ($result_image->num_rows == 0) {
        header("Location: ../index1.php?error=Produit introuvable");
        exit();
    }

    $row_image = $result_image->fetch_assoc();
    $image = $row_image['image1'];
    $stmt_image->close();

    // vérifier si le produit existe déjà dans le panier
    $check_stmt = $con->prepare("SELECT * FROM panier WHERE utilisateur_id = ? AND produit_id = ?");
    $check_stmt->bind_param("ii", $user_id, $produit_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // si le produit existe déjà mettre à jour la quantité
        $update_stmt = $con->prepare("UPDATE panier SET quantite = quantite + ? WHERE utilisateur_id = ? AND produit_id = ?");
        $update_stmt->bind_param("iii", $quantite, $user_id, $produit_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // insérer un nouveau produit dans le panier avec l'image
        $insert_stmt = $con->prepare("INSERT INTO panier (utilisateur_id, produit_id, quantite, prix, image) VALUES (?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("iiids", $user_id, $produit_id, $quantite, $prix, $image);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    $check_stmt->close();

    header("Location: view_cart.php?success=Produit ajouté au panier");
    exit();
} else {
    // Redirection si les données du form sont manquantes
    header("Location: ../index1.php?error=Données manquantes");
    exit();
}
?>