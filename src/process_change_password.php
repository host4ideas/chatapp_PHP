<?php
// Relative paths from change_password.php
require_once '../db.php';
require_once '../functions.php';

$erpassw = false;
$evOK = true;
$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST['passw'])) {
        $errors[] = "Password cannot be empty";
        $erpassw = true;
        $evOK = false;
    } else {
        $passw1 = htmlspecialchars($_POST['passw']);
        if (!checkpassw($passw1)) {
            $errors[] = "Check password restrictions";
            $erpassw = true;
            $evOK = false;
        }
    }
    if (empty($_POST['passw2'])) {
        $errors[] = "Password repeat cannot be empty";
        $erpassw = true;
        $evOK = false;
    } else {
        $passw2 = htmlspecialchars($_POST['passw2']);
        if (strcmp($passw1, $passw2) != 0) {
            $errors[] = "Passwords do not match";
            $erpassw = true;
            $evOK = false;
        }
    }

    // Add database alter
    if ($evOK) {
        $id = $_POST['id'];
        $hashed_password = password_hash($passw1, PASSWORD_DEFAULT);
        $sql_srt = "UPDATE users SET passwd = '$hashed_password' WHERE codUser like '$id'";
        $result = $db->query($sql_srt);
        $codUSer = $result->fetch();
        header("Location: ./login_already.php");
        die();
    }
}
