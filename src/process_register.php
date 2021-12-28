<?php
require_once '../functions.php';
require_once '../db.php';
$erFirstName = $erLastName = $erAlias = $ermail = $erpassw = $erage = $erAvatarURI = false;
$firstName = $lastName = $alias = $mail = $passw1 = $passw2 = $age = $gender = $avatarURI = "";
$evOK = true;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['alias'])) {
        $errors[] = "Alias can not be empty";
        $erAlias = true;
        $evOK = false;
    } else {
        $alias = htmlspecialchars($_POST['alias']);
        if (!checkAlias($alias)) {
            $errors[] = "Check alias length...";
            $erAlias = true;
            $evOK = false;
        } else {
            $sql_str = "SELECT codUser from users where alias like '$alias'";
            $resul = $db->query($sql_str);
            if ($resul->rowCount() > 0) {
                $errors[] = "That alias is already registered";
                $erAlias = true;
                $evOK = false;
            }
        }
    }

    if (empty($_POST['firstName'])) {
        $errors[] = "First name can not be empty";
        $erFirstName = true;
        $evOK = false;
    } else {
        $firstName = htmlspecialchars($_POST['firstName']);
    }

    if (empty($_POST['lastName'])) {
        $errors[] = "Last name cannot be empty";
        $erLastName = true;
        $evOK = false;
    } else {
        $lastName = htmlspecialchars($_POST['lastName']);
    }

    if (empty($_POST['mail'])) {
        $errors[] = "Mail cannot be empty";
        $ermail = true;
        $evOK = false;
    } else {
        $mail = htmlspecialchars($_POST['mail']);
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Check mail";
            $ermail = true;
            $evOK = false;
        } else {
            $sql_str = "SELECT codUser from users where email like '$mail'";
            $resul = $db->query($sql_str);
            if ($resul->rowCount() > 0) {
                $errors[] = "There is already an account with that email";
                $ermail = true;
                $evOK = false;
            }
        }
    }
    if (empty($_POST['passw'])) {
        $errors[] = "Password can not be empty";
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
        $errors[] = "Password repeat can not be empty";
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

    if ($_FILES["avatar"]['size'] > 0) {
        $target_dir = "../uploads/avatars/";
        $check = getimagesize($_FILES["avatar"]["tmp_name"]);
        if ($check === false) {
            $image = false;
        } else {
            $image = true;
        }
        $uploadResult = uploadFile($_FILES["avatar"], $target_dir, true);
        if ($uploadResult[0] !== ".") {
            $errors = $uploadResult;
            $evOK = false;
        } else {
            $avatarURI = substr($uploadResult, 1, strlen($uploadResult));
        }
        if ($evOK == false) {
            $avatarURI = "./uploads/avatars/default/default_avatar.jpg";
        }
    } else {
        $avatarURI = "./uploads/avatars/default/default_avatar.jpg";
    }
    if ($evOK) {
        try {
            // Insert hashed password in the DB
            $hashed_password = password_hash($passw1, PASSWORD_DEFAULT);
            // Insert current time to expire the registration verification
            $t = date('Y-m-d H:i:s');
            $sql_str = "INSERT INTO users VALUES (null, '$firstName', '$lastName', '$alias', '$mail', '$hashed_password', '$avatarURI', '$t', 0)";
            $resul = $db->query($sql_str);
            $codUser = $db->lastInsertId();
            header("Location: ../send_confirmation_register.php?id=$codUser&mail=$mail");
        } catch (PDOException $e) {
            $errors[] = "Database error: " .  $e->getMessage();
        }
    }
}
