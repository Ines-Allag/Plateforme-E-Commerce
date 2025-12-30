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
        // CHANGEMENT: Table 'utilisateurs' avec filtre role='admin'
        $sql = "SELECT * FROM utilisateurs WHERE nom_utilisateur='$uname' AND mot_de_passe='$pass' AND role='admin'";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            // CHANGEMENT: Utiliser 'nom_utilisateur' et 'mot_de_passe'
            if ($row['nom_utilisateur'] === $uname && $row['mot_de_passe'] === $pass) {
                $_SESSION['name'] = $row['nom_utilisateur']; 
                $_SESSION['id'] = $row['id'];
                $_SESSION['role'] = 'admin'; // NOUVEAU: Stocker le rôle
                header("Location: DashboardAdmin.php");
                exit();
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