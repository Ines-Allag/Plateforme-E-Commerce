<?php 
session_start(); 
include "../config.php"; // Assurez-vous que $conn est défini dans ce fichier

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
        header("Location: signup.php?error=User Name is required&$user_data");
        exit();
    } else if (empty($pass)) {
        header("Location: signup.php?error=Password is required&$user_data");
        exit();
    } else if (empty($re_pass)) {
        header("Location: signup.php?error=Re Password is required&$user_data");
        exit();
    } else if (empty($name)) {
        header("Location: signup.php?error=Name is required&$user_data");
        exit();
    } else if ($pass !== $re_pass) {
        header("Location: signup.php?error=The confirmation password does not match&$user_data");
        exit();
    } else {
        // Vérifier si le nom d'utilisateur existe déjà
        $sql = "SELECT * FROM clients WHERE name='$uname'";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
            header("Location: signup.php?error=The username is taken, try another&$user_data");
            exit();
        } else {
            // Insérer les données utilisateur dans la base
            $sql2 = "INSERT INTO clients(name,password) VALUES('$uname', '$pass')";
            $result2 = mysqli_query($con, $sql2);

            if ($result2) {
                header("Location: signup.php?success=Your account has been created successfully");
                exit();
            } else {
                header("Location: signup.php?error=An unknown error occurred&$user_data");
                exit();
            }
        }
    }
} else {
    header("Location: signup.php");
    exit();
}
?>
