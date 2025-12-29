<?php  
session_start(); // Démarrer la session
include('../config.php');

// Ajouter ou modifier un produit
if (isset($_POST['ajouter'])) {
    // Récupérer les données du formulaire
    $NAME = mysqli_real_escape_string($con, $_POST['nom']);  // Sécurisation des entrées utilisateur
    $DESCRIPTION = mysqli_real_escape_string($con, $_POST['description']);
    $CATEGORIE = mysqli_real_escape_string($con, $_POST['categorie']);  // ID de la catégorie
    $PRIX = mysqli_real_escape_string($con, $_POST['prix']);
    // Gérer les 3 images
$IMAGE1 = $_FILES['img1'];
$IMAGE2 = isset($_FILES['img2']) ? $_FILES['img2'] : null;
$IMAGE3 = isset($_FILES['img3']) ? $_FILES['img3'] : null;

$image1_up = "imgs/default.jpg";
$image2_up = null;
$image3_up = null;

// Traiter image 1
if (isset($IMAGE1['name']) && !empty($IMAGE1['name'])) {
    $image1_name = $IMAGE1['name'];
    $image1_tmp = $IMAGE1['tmp_name'];
    $image1_up = "imgs/" . $image1_name;
    move_uploaded_file($image1_tmp, $image1_up);
}

// Traiter image 2
if (isset($IMAGE2['name']) && !empty($IMAGE2['name'])) {
    $image2_name = $IMAGE2['name'];
    $image2_tmp = $IMAGE2['tmp_name'];
    $image2_up = "imgs/" . $image2_name;
    move_uploaded_file($image2_tmp, $image2_up);
}

// Traiter image 3
if (isset($IMAGE3['name']) && !empty($IMAGE3['name'])) {
    $image3_name = $IMAGE3['name'];
    $image3_tmp = $IMAGE3['tmp_name'];
    $image3_up = "imgs/" . $image3_name;
    move_uploaded_file($image3_tmp, $image3_up);
}

    // Si une image est téléchargée, vérifier si le fichier est valide
    if (isset($IMAGE['name']) && !empty($IMAGE['name'])) {
        $image_name = $IMAGE['name']; // Récupérer le nom de l'image téléchargée
        $image_tmp = $IMAGE['tmp_name']; // Récupérer le chemin temporaire de l'image
        $image_up = "imgs/" . $image_name; // Dossier où l'image sera stockée

        // Vérifier si l'extension du fichier est une image valide (jpg, png, jpeg, gif)
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        if (in_array($image_extension, $allowed_extensions)) {
            // Déplacer l'image téléchargée vers le dossier de destination
            if (move_uploaded_file($image_tmp, $image_up)) {
                // L'image a été déplacée avec succès
            } else {
                echo "<script>alert('Erreur lors du téléchargement de l\'image.');</script>";
            }
        } else {
            echo "<script>alert('Type de fichier non autorisé pour l\'image.');</script>";
        }
    } else {
        // Si aucune image n'est téléchargée, utiliser une image par défaut
        $image_up = "imgs/default.jpg";  // Image par défaut
    }

    // Si l'ID du produit est spécifié, c'est une mise à jour
    if (isset($_POST['id'])) {
        // Mise à jour du produit
        $id = $_POST['id'];
        $update_query = "UPDATE produits 
                         SET name = '$NAME', description = '$DESCRIPTION', prix = '$PRIX', img = '$image_up', categorie = '$CATEGORIE' 
                         WHERE id = '$id'";

        if (mysqli_query($con, $update_query)) {
            echo "<script>alert('Produit modifié correctement');</script>";
        } else {
            echo "<script>alert('Erreur lors de la modification du produit');</script>";
        }
    } else {
        // Insertion du produit dans la base de données
       $insert = "INSERT INTO produits (name, description, prix, img1, img2, img3, categorie) 
           VALUES ('$NAME', '$DESCRIPTION', '$PRIX', '$image1_up', '$image2_up', '$image3_up', '$CATEGORIE')";

        if (mysqli_query($con, $insert)) {
            echo "<script>alert('Produit ajouté correctement');</script>";
        } else {
            echo "<script>alert('Erreur lors de l\'ajout du produit dans la base de données');</script>";
        }
    }

    // Rediriger après l'ajout ou la mise à jour
    header('location: Gstock.php');
}
?>
