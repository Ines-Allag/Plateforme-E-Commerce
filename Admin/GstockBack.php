<?php  
session_start();
include('../config.php');

// Ajouter ou modifier un produit
if (isset($_POST['ajouter'])) {
    // Récupérer les données du formulaire
    $NAME = mysqli_real_escape_string($con, $_POST['nom']);
    $DESCRIPTION = mysqli_real_escape_string($con, $_POST['description']);
    $CATEGORIE = mysqli_real_escape_string($con, $_POST['categorie']);
    $PRIX = mysqli_real_escape_string($con, $_POST['prix']);
    
    // Gérer les 3 images
    $IMAGE1 = $_FILES['img1'];
    $IMAGE2 = isset($_FILES['img2']) ? $_FILES['img2'] : null;
    $IMAGE3 = isset($_FILES['img3']) ? $_FILES['img3'] : null;

    $image1_up = "imgs/default.jpg";
    $image2_up = null;
    $image3_up = null;

    $allowed_extensions = array("jpg", "jpeg", "png", "gif");

    // Traiter image 1 (obligatoire)
    if (isset($IMAGE1['name']) && !empty($IMAGE1['name'])) {
        $image1_name = $IMAGE1['name'];
        $image1_tmp = $IMAGE1['tmp_name'];
        $image1_extension = strtolower(pathinfo($image1_name, PATHINFO_EXTENSION));

        if (in_array($image1_extension, $allowed_extensions)) {
            $image1_up = "imgs/" . $image1_name;
            if (!move_uploaded_file($image1_tmp, $image1_up)) {
                echo "<script>alert('Erreur lors du téléchargement de l\\'image 1.');</script>";
            }
        } else {
            echo "<script>alert('Type de fichier non autorisé pour l\\'image 1.');</script>";
        }
    }

    // Traiter image 2 (optionnelle)
    if (isset($IMAGE2['name']) && !empty($IMAGE2['name'])) {
        $image2_name = $IMAGE2['name'];
        $image2_tmp = $IMAGE2['tmp_name'];
        $image2_extension = strtolower(pathinfo($image2_name, PATHINFO_EXTENSION));

        if (in_array($image2_extension, $allowed_extensions)) {
            $image2_up = "imgs/" . $image2_name;
            move_uploaded_file($image2_tmp, $image2_up);
        } else {
            echo "<script>alert('Type de fichier non autorisé pour l\\'image 2.');</script>";
        }
    }

    // Traiter image 3 (optionnelle)
    if (isset($IMAGE3['name']) && !empty($IMAGE3['name'])) {
        $image3_name = $IMAGE3['name'];
        $image3_tmp = $IMAGE3['tmp_name'];
        $image3_extension = strtolower(pathinfo($image3_name, PATHINFO_EXTENSION));

        if (in_array($image3_extension, $allowed_extensions)) {
            $image3_up = "imgs/" . $image3_name;
            move_uploaded_file($image3_tmp, $image3_up);
        } else {
            echo "<script>alert('Type de fichier non autorisé pour l\\'image 3.');</script>";
        }
    }

    // Si l'ID du produit est spécifié, c'est une mise à jour
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        // CHANGEMENT: Colonnes 'nom', 'image1', 'image2', 'image3'
        $update_query = "UPDATE produits 
                         SET nom = '$NAME', description = '$DESCRIPTION', prix = '$PRIX', 
                             image1 = '$image1_up', image2 = '$image2_up', image3 = '$image3_up', 
                             categorie = '$CATEGORIE' 
                         WHERE id = '$id'";

        if (mysqli_query($con, $update_query)) {
            echo "<script>alert('Produit modifié correctement');</script>";
        } else {
            echo "<script>alert('Erreur lors de la modification du produit');</script>";
        }
    } else {
        // CHANGEMENT: Colonnes 'nom', 'image1', 'image2', 'image3'
        $insert = "INSERT INTO produits (nom, description, prix, image1, image2, image3, categorie) 
                   VALUES ('$NAME', '$DESCRIPTION', '$PRIX', '$image1_up', '$image2_up', '$image3_up', '$CATEGORIE')";

        if (mysqli_query($con, $insert)) {
            echo "<script>alert('Produit ajouté correctement');</script>";
        } else {
            echo "<script>alert('Erreur: " . mysqli_error($con) . "');</script>";
        }
    }

    // Rediriger après l'ajout ou la mise à jour
    header('location: Gstock.php');
    exit();
}
?>