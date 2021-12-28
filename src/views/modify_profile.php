<?php
require_once '../db.php';
require_once '../functions.php';
session_start();
if (!isset($_SESSION['codUser'])) {
    header("Location: ../logout.php");
}
$codSession = $_SESSION['codUser'];
if (isset($_GET['id'])) {
    $codUser = $_GET['id'];
} else {
    header("Location: ./chats_home.php?id=" . $codSession);
}
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['firstName'])) {
        $firstName = $_POST['firstName'];
        $sql_srt = "UPDATE users SET firstName = '$firstName' WHERE codUser like '$codSession'";
        $db->query($sql_srt);
    }

    if (!empty($_POST['lastName'])) {
        $lastName = $_POST['lastName'];
        $sql_srt = "UPDATE users SET lastName = '$lastName' WHERE codUser like '$codSession'";
        $db->query($sql_srt);
    }

    if (!empty($_POST['alias'])) {
        $alias = htmlspecialchars($_POST['alias']);
        if (!checkAlias($alias)) {
            $errors[] = "Check alias length...";
        } else {
            $sql_str = "SELECT codUser from users where alias like '$alias'";
            $resul = $db->query($sql_str);
            if ($resul->rowCount() > 0) {
                $errors[] = "That alias is already registered";
            } else {
                $sql_srt = "UPDATE users SET alias = '$alias' WHERE codUser like '$codSession'";
                $db->query($sql_srt);
            }
        }
    }

    if (!empty($_POST['mail'])) {
        $mail = htmlspecialchars($_POST['mail']);
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Check mail";
        } else {
            $sql_str = "SELECT codUser from users where email like '$mail'";
            $resul = $db->query($sql_str);
            if ($resul->rowCount() > 0) {
                $errors[] = "There is already an account with that email";
            } else {
                $sql_srt = "UPDATE users SET email = '$mail' WHERE codUser like '$codSession'";
                $db->query($sql_srt);
            }
        }
    }

    if ($_FILES['avatarInput']['size'] > 0) {
        $uploadResult = uploadFile($_FILES['avatarInput'], "./uploads/avatars/", true);
        if ($uploadResult[0] !== ".") {
            $errors = $uploadResult;
        } else {
            $sql_srt = "UPDATE users SET avatarURI = '$uploadResult' WHERE codUser like '$codSession'";
            $db->query($sql_srt);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Modify Profile</title>
</head>

<body>
    <form action="./modify_profile.php?id=<?php echo $_SESSION['codUser'] ?>" method="POST" enctype="multipart/form-data">
        <?php
        $ins = "SELECT firstName, lastName, alias, email, avatarURI from users WHERE codUser like '$codUser'";
        $resul = $db->query($ins);
        $resul = $resul->fetchAll();
        $firstName = $resul[0]['firstName'];
        $lastName = $resul[0]['lastName'];
        $alias = $resul[0]['alias'];
        $email = $resul[0]['email'];
        $avatarURI = '.' . $resul[0]['avatarURI'];
        $inputNewAvatar = '<div class="input-group mb-3">
    <label class="input-group-text" for="avatar">Upload new avatar</label>
    <input type="file" name="avatarInput" class="form-control" id="avatar">
    </div>';
        $inputFirstName = '<input type="text" class="form-control" name="firstName" placeholder="New first name" aria-label="New first name" aria-describedby="button-addon2">';
        $inputLastName = '<input type="text" class="form-control" name="lastName" placeholder="New last name" aria-label="New last name" aria-describedby="button-addon2">';
        $inputAlias = '<input type="text" aria-label="New Alias" name="alias" placeholder="New alias" class="form-control">';
        $inputMail = '<input type="text" aria-label="New Email" name="mail" placeholder="New email" class="form-control">';
        echo '<table class="table table-dark">
    <thead>
        <tr>
            <th scope="col">First Name</th>
            <th scope="col">Last Name</th>
            <th scope="col">Alias</th>
            <th scope="col">Email</th>
            <th scope="col">Avatar</th>
        </tr>
    </thead>
    <tbody>';
        echo "<tr name=$alias>
            <td>$firstName</td>
            <td>$lastName</td>
            <td>$alias</td>
            <td>$email</td>
            <td><img src='$avatarURI' width='100px' height='100px'/></td>
        </tr>
        <tr name='inputs'>
            <td>$inputFirstName</td>
            <td>$inputLastName</td>
            <td>$inputAlias</td>
            <td>$inputMail</td>
            <td>$inputNewAvatar</td>
        </tr>";
        echo
        '</tbody>
    </table>';
        ?>
        <section id="errors">
            <?php
            if (!empty($errors)) {
                echo "<ul style='list-style: none; border-top: 1px solid rgba(218, 51, 10, 0.6); border-bottom: 1px solid rgba(218, 51, 10, 0.6); margin: 10px;'>";
                foreach ($errors as $error) {
                    echo "<li> $error </li>";
                }
                echo "</ul>";
            }
            ?>
        </section>
        <a class="btn btn-danger" href="./chats_home.php?id=<?php echo $codSession ?>" role="button">Return</a>
        <a class="btn btn-info" href="./change_password.php?id=<?php echo $codSession ?>" role="button">Change password</a>
        <button class="btn btn-outline-primary" type="submit">Submit changes</button>
    </form>
</body>

</html>