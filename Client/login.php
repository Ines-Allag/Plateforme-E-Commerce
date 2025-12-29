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
        header("Location: index.php?error=User Name is required");
        exit();
    } else if (empty($pass)) {
        header("Location: index.php?error=Password is required");
        exit();
    } else {
        // Prévention des injections SQL
        $sql = "SELECT * FROM clients WHERE name='$uname' AND password='$pass'";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            // Validation des informations utilisateur
            if ($row['name'] === $uname && $row['password'] === $pass) {
                $_SESSION['name'] = $row['name']; 
                $_SESSION['id'] = $row['id'];
                header("Location: ../index1.php");
                exit();
            } else {
                header("Location: index.php?error=Incorrect User name or password");
                exit();
            }
        } else {
            header("Location: index.php?error=Incorrect User name or password");
            exit();
        }
    }

} else {
    header("Location: index.php");
    exit();
}
?>
