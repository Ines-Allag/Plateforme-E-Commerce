<?php 
session_start(); 
include "../config.php";

if (isset($_POST['uname']) && isset($_POST['password']) && isset($_POST['name']) && isset($_POST['re_password'])) {

    function validate($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $uname = validate($_POST['uname']);
    $pass = validate($_POST['password']);
    $re_pass = validate($_POST['re_password']);
    $name = validate($_POST['name']);

    $user_data = 'uname=' . urlencode($uname) . '&name=' . urlencode($name);

    // Vérifications des champs obligatoires
    if (empty($uname)) {
        header("Location: signup.php?error=Le nom d'utilisateur est requis&$user_data");
        exit();
    } else if (empty($pass)) {
        header("Location: signup.php?error=Le mot de passe est requis&$user_data");
        exit();
    } else if (empty($re_pass)) {
        header("Location: signup.php?error=La confirmation est requise&$user_data");
        exit();
    } else if (empty($name)) {
        header("Location: signup.php?error=Le nom est requis&$user_data");
        exit();
    } else if ($pass !== $re_pass) {
        header("Location: signup.php?error=Les mots de passe ne correspondent pas&$user_data");
        exit();
    } else {
        // CHANGEMENT: Vérifier dans 'utilisateurs' avec 'nom_utilisateur'
        $sql = "SELECT * FROM utilisateurs WHERE nom_utilisateur='$uname' AND role='admin'";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
            header("Location: signup.php?error=Ce nom d'utilisateur est déjà pris&$user_data");
            exit();
        } else {
            // CHANGEMENT: Insérer dans 'utilisateurs' avec role='admin'
            $sql2 = "INSERT INTO utilisateurs(nom_utilisateur, mot_de_passe, role) VALUES('$uname', '$pass', 'admin')";
            $result2 = mysqli_query($con, $sql2);

            if ($result2) {
                header("Location: signup.php?success=Votre compte admin a été créé avec succès");
                exit();
            } else {
                header("Location: signup.php?error=Une erreur inconnue est survenue&$user_data");
                exit();
            }
        }
    }
} else {
    header("Location: signup.php");
    exit();
}
?>