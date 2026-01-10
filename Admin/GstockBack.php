<?php  
session_start();

// Vérifier si l'admin est connecté
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=Accès non autorisé");
    exit();
}

include('../config.php');

// FONCTION POUR UPLOADER UNE IMAGE
function uploadImage($file, $index) {
    if (!isset($file['name']) || empty($file['name'])) {
        return null;
    }

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Vérifier l'extension
    if (!in_array($file_extension, $allowed_extensions)) {
        return ['error' => "Format non autorisé pour l'image $index"];
    }
    
    // Vérifier la taille (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['error' => "L'image $index est trop volumineuse (max 5MB)"];
    }
    
    // CORRECTION : Upload directement dans imgs/
    $upload_dir = "../imgs/";
    
    // Créer UNIQUEMENT le dossier imgs/ s'il n'existe pas (PAS produits/)
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Générer un nom unique pour éviter les conflits
    $unique_name = uniqid() . '_' . time() . '.' . $file_extension;
    $destination = $upload_dir . $unique_name;
    
    // Déplacer le fichier
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Retourner le chemin RELATIF pour la base de données
        return "imgs/" . $unique_name;
    } else {
        return ['error' => "Erreur lors de l'upload de l'image $index"];
    }
}

// AJOUTER UN NOUVEAU PRODUIT
if (isset($_POST['ajouter'])) {
    $nom = mysqli_real_escape_string($con, trim($_POST['nom']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));
    $categorie = mysqli_real_escape_string($con, trim($_POST['categorie']));
    $prix = floatval($_POST['prix']);
    $quantite_stock = intval($_POST['quantite_stock']);
    
    // Validation
    if (empty($nom) || empty($description) || empty($categorie) || $prix <= 0) {
        header("Location: Gstock.php?error=Tous les champs obligatoires doivent être remplis");
        exit();
    }
    
    // Upload des images
    $images = [];
    $error = null;
    
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_FILES["image$i"]) && $_FILES["image$i"]['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = uploadImage($_FILES["image$i"], $i);
            
            if (is_array($upload_result) && isset($upload_result['error'])) {
                $error = $upload_result['error'];
                break;
            }
            
            $images[$i] = $upload_result;
        } else {
            $images[$i] = null;
        }
    }
    
    // Si erreur lors de l'upload
    if ($error) {
        header("Location: Gstock.php?error=" . urlencode($error));
        exit();
    }
    
    // Vérifier qu'au moins une image est fournie
    if (empty($images[1])) {
        header("Location: Gstock.php?error=Au moins une image est requise");
        exit();
    }
    
    // Insertion dans la base de données
    $sql = "INSERT INTO produits (nom, description, categorie, prix, quantite_stock, image1, image2, image3, date_ajout) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssisss", 
        $nom, 
        $description, 
        $categorie, 
        $prix, 
        $quantite_stock, 
        $images[1], 
        $images[2], 
        $images[3]
    );
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: stock_management.php?success=Produit ajouté avec succès");
        exit();
    } else {
        header("Location: Gstock.php?error=Erreur lors de l'ajout du produit");
        exit();
    }
}

// MODIFIER UN PRODUIT EXISTANT
if (isset($_POST['modifier'])) {
    $product_id = intval($_POST['product_id']);
    $nom = mysqli_real_escape_string($con, trim($_POST['nom']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));
    $categorie = mysqli_real_escape_string($con, trim($_POST['categorie']));
    $prix = floatval($_POST['prix']);
    $quantite_stock = intval($_POST['quantite_stock']);
    
    // Validation
    if (empty($nom) || empty($description) || empty($categorie) || $prix <= 0) {
        header("Location: Gstock.php?edit_id=$product_id&error=Tous les champs obligatoires doivent être remplis");
        exit();
    }
    
    // Récupérer les images actuelles
    $query = "SELECT image1, image2, image3 FROM produits WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $current_product = mysqli_fetch_assoc($result);
    
    $images = [
        1 => $current_product['image1'],
        2 => $current_product['image2'],
        3 => $current_product['image3']
    ];
    
    // Upload de nouvelles images si fournies
    $error = null;
    
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_FILES["image$i"]) && $_FILES["image$i"]['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = uploadImage($_FILES["image$i"], $i);
            
            if (is_array($upload_result) && isset($upload_result['error'])) {
                $error = $upload_result['error'];
                break;
            }
            
            // Supprimer l'ancienne image si elle existe
            if (!empty($images[$i])) {
                $old_file_path = "../" . $images[$i];
                if (file_exists($old_file_path) && $images[$i] !== 'imgs/default.jpg') {
                    @unlink($old_file_path);
                }
            }
            
            $images[$i] = $upload_result;
        }
    }
    
    // Si erreur lors de l'upload
    if ($error) {
        header("Location: Gstock.php?edit_id=$product_id&error=" . urlencode($error));
        exit();
    }
    
    // Mise à jour dans la base de données
    $sql = "UPDATE produits 
            SET nom = ?, description = ?, categorie = ?, prix = ?, quantite_stock = ?, 
                image1 = ?, image2 = ?, image3 = ?
            WHERE id = ?";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssisssi", 
        $nom, 
        $description, 
        $categorie, 
        $prix, 
        $quantite_stock, 
        $images[1], 
        $images[2], 
        $images[3],
        $product_id
    );
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: stock_management.php?success=Produit modifié avec succès");
        exit();
    } else {
        header("Location: Gstock.php?edit_id=$product_id&error=Erreur lors de la modification");
        exit();
    }
}

// Si aucune action n'est définie
header("Location: stock_management.php");
exit();
?>