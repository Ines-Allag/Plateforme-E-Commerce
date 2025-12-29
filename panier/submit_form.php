<?php
session_start();
include('../config.php');

// Vérification de la connexion utilisateur
if (!isset($_SESSION['id'])) {
    header("Location: Client/index.php?error=Veuillez vous connecter");
    exit();
}

$user_id = $_SESSION['id'];

// Traitement des données du formulaire
$name = $_POST['name'];
$address = $_POST['address'];
$phone = $_POST['phone'];

// Ici, vous pouvez traiter les données (par exemple, les enregistrer dans une table de commandes)

// Vider le panier de l'utilisateur
$stmt = $con->prepare("DELETE FROM panier WHERE client_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Vous pouvez rediriger ou simplement envoyer une réponse de succès ici
header("Location: view_cart.php");
exit();
?>
