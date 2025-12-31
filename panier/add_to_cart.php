<?php
session_start();
include('../config.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['nom_utilisateur']) || !isset($_SESSION['id'])) {  // CHANGEMENT: 'name' → 'nom_utilisateur'
    header("Location: ../Client/index.php");
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
        header("Location: ../index1.php?error=Quantité invalide.");
        exit();
    }

    // NOUVEAU : Check stock disponible
    $stmt_stock = $con->prepare("SELECT quantite_stock, image1 FROM produits WHERE id = ?");
    $stmt_stock->bind_param("i", $produit_id);
    $stmt_stock->execute();
    $stock_result = $stmt_stock->get_result();
    $stock_data = $stock_result->fetch_assoc();
    
    if ($stock_data['quantite_stock'] < $quantite) {
        header("Location: ../product_details.php?id=$produit_id&error=Stock insuffisant (seulement " . $stock_data['quantite_stock'] . " disponibles)");
        exit();
    }

    $img = $stock_data['image1'];  // Récupère image1

    // Vérifier si le produit existe déjà dans le panier
    $check_query = "SELECT * FROM panier WHERE utilisateur_id = $user_id AND produit_id = $produit_id";
    $result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Si déjà dans panier, update quantité
        $update_query = "UPDATE panier 
                         SET quantite = quantite + $quantite 
                         WHERE utilisateur_id = $user_id AND produit_id = $produit_id";
        mysqli_query($con, $update_query);
    } else {
        // Insert nouveau
        $insert_query = "INSERT INTO panier (utilisateur_id, produit_id, quantite, prix, image) 
                         VALUES ($user_id, $produit_id, $quantite, $prix, '$img')";
        mysqli_query($con, $insert_query);
    }

    // Redirection avec succès
    header("Location: ../index1.php?success=Produit ajouté au panier");
    exit();
} else {
    header("Location: ../index1.php?error=Paramètres manquants");
    exit();
}
?>