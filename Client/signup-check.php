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
    $name = validate($_POST['name']); // Note: cette valeur peut être stockée dans une colonne 'nom_complet' si vous en ajoutez une, sinon elle n'est pas utilisée ici.

    $user_data = 'uname=' . urlencode($uname) . '&name=' . urlencode($name);

    if (empty($uname)) {
        header("Location: signup.php?error=Le nom d'utilisateur est requis&$user_data");
        exit();
    } else if (empty($pass)) {
        header("Location: signup.php?error=Le mot de passe est requis&$user_data");
        exit();
    } else if (empty($re_pass)) {
        header("Location: signup.php?error=La confirmation est requise&$user_data");
        exit();
    } else if ($pass !== $re_pass) {
        header("Location: signup.php?error=Les mots de passe ne correspondent pas&$user_data");
        exit();
    } else {
        // CORRECTION ICI : Table 'utilisateurs' et colonne 'nom_utilisateur'
        $sql = "SELECT * FROM utilisateurs WHERE nom_utilisateur='$uname'";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
            header("Location: signup.php?error=Ce nom d'utilisateur est déjà pris&$user_data");
            exit();
        } else {
            // CORRECTION ICI : Table 'utilisateurs', colonnes 'nom_utilisateur', 'mot_de_passe' et 'role'
            $sql2 = "INSERT INTO utilisateurs(nom_utilisateur, mot_de_passe, role) VALUES('$uname', '$pass', 'client')";
            $result2 = mysqli_query($con, $sql2);

            if ($result2) {
                header("Location: signup.php?success=Votre compte a été créé avec succès");
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