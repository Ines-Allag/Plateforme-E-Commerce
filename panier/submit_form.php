<?php
session_start();
include('../config.php');

if (!isset($_SESSION['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view_cart.php");
    exit();
}

$user_id = $_SESSION['id'];

// Récupérer le nom de l'utilisateur depuis la base de données
$stmt_user = $con->prepare("SELECT nom_utilisateur FROM utilisateurs WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user_data = $user_result->fetch_assoc();
$nom_livraison = $user_data['nom_utilisateur'];

// Récupérer les données du formulaire
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$adresse = trim($_POST['address']);
$telephone = trim($_POST['phone']);

// Validation
if (empty($adresse) || empty($telephone)) {
    header("Location: view_cart.php?error=Veuillez remplir tous les champs obligatoires");
    exit();
}

// Commencer une transaction
$con->begin_transaction();

try {
    // Récupérer les articles du panier
    $stmt = $con->prepare("
        SELECT c.produit_id, c.quantite, c.prix, p.nom
        FROM panier c
        JOIN produits p ON c.produit_id = p.id
        WHERE c.utilisateur_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_items = $stmt->get_result();

    if ($cart_items->num_rows === 0) {
        throw new Exception("Votre panier est vide");
    }

    // Calculer le total
    $total = 0;
    $items = [];
    while ($item = $cart_items->fetch_assoc()) {
        $total += $item['prix'] * $item['quantite'];
        $items[] = $item;
    }

    // Créer la commande
    $stmt_order = $con->prepare("
        INSERT INTO commandes (utilisateur_id, total, statut, nom_livraison, adresse_livraison, telephone_livraison, email_livraison)
        VALUES (?, ?, 'en_attente', ?, ?, ?, ?)
    ");
    $stmt_order->bind_param("idssss", $user_id, $total, $nom_livraison, $adresse, $telephone, $email);
    
    if (!$stmt_order->execute()) {
        throw new Exception("Erreur lors de la création de la commande");
    }

    $commande_id = $con->insert_id;

    // Ajouter les détails de la commande
    $stmt_details = $con->prepare("
        INSERT INTO details_commande (commande_id, produit_id, nom_produit, quantite, prix_unitaire)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($items as $item) {
        $stmt_details->bind_param("iisid", 
            $commande_id, 
            $item['produit_id'], 
            $item['nom'], 
            $item['quantite'], 
            $item['prix']
        );
        
        if (!$stmt_details->execute()) {
            throw new Exception("Erreur lors de l'ajout des détails de la commande");
        }
    }

    // Vider le panier
    $stmt_clear = $con->prepare("DELETE FROM panier WHERE utilisateur_id = ?");
    $stmt_clear->bind_param("i", $user_id);
    
    if (!$stmt_clear->execute()) {
        throw new Exception("Erreur lors de la suppression du panier");
    }

    // Valider la transaction
    $con->commit();

    // Rediriger vers la page de confirmation
    header("Location: mes_commandes.php?success=Commande créée avec succès");
    exit();

} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    $con->rollback();
    header("Location: view_cart.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>