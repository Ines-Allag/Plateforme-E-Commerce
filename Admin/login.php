<?php 
session_start(); 
include "../config.php"; 

if (isset($_POST['uname']) && isset($_POST['password'])) {

    function validate($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $uname = validate($_POST['uname']);
    $pass = validate($_POST['password']);

    if (empty($uname)) {
        header("Location: index.php?error=Le nom d'utilisateur est requis");
        exit();
    } else if (empty($pass)) {
        header("Location: index.php?error=Le mot de passe est requis");
        exit();
    } else {
        // Requête sécurisée
        $sql = "SELECT * FROM utilisateurs WHERE nom_utilisateur=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $uname);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            // VÉRIFICATION DU HASH
            if (password_verify($pass, $row['mot_de_passe'])) {
                $_SESSION['nom_utilisateur'] = $row['nom_utilisateur'];
                $_SESSION['id'] = $row['id'];
                $_SESSION['role'] = $row['role'];

                if ($row['role'] === 'admin') {
                    header("Location: DashboardAdmin.php");
                    exit();
                } else {
                    header("Location: index.php?error=Rôle incorrect");
                    exit();
                }
            } else {
                header("Location: index.php?error=Identifiants incorrects");
                exit();
            }
        } else {
            header("Location: index.php?error=Identifiants incorrects");
            exit();
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>