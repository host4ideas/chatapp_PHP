<?php
require '../db.php';
session_start();
if (!isset($_SESSION['codUser']) || $_SESSION['codUser'] != 1) {
    header("Location: ../logout.php");
    die();
}
if (isset($_GET['codUser'])) {
    $codUser = $_GET['codUser'];
    $sql_str = "DELETE FROM users WHERE codUser like '$codUser'";
    $resul = $db->query($sql_str);
    $sql_str = "DELETE FROM participate WHERE codUser like '$codUser'";
    $resul = $db->query($sql_str);
    header("Location: ./admin_zone.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Admin Zone</title>
</head>

<body>
    <?php
    $ins = "SELECT * from users";
    $resul = $db->query($ins);
    $arrayResult = $resul->fetchAll();
    echo '<table class="table table-dark">
    <thead>
        <tr>
            <th scope="col">User Code</th>
            <th scope="col">First Name</th>
            <th scope="col">Last Name</th>
            <th scope="col">Alias</th>
            <th scope="col">Email</th>
            <th scope="col">Password</th>
            <th scope="col">Avatar URI</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
    ';
    foreach ($arrayResult as $user) {
        // Added remote server functionality
        if ($_SERVER['REMOTE_ADDR'] == "1::1" || $_SERVER['REMOTE_ADDR'] == "::1") {
            $button = "<td><a class='btn btn-primary' href='./admin_zone.php?codUser=$codUser' role='button'>Delete User</a></td>";
        }
        $codUser = $user['codUser'];
        $firstName = $user['firstName'];
        $lastName = $user['lastName'];
        $alias = $user['alias'];
        $email = $user['email'];
        $passwd = $user['passwd'];
        $avatarURI = $user['avatarURI'];
        if ($user['codUser'] == 1) {
            echo "<tr name=$codUser>
            <th scope='row'>$codUser</th>
            <td>$firstName</td>
            <td>$lastName</td>
            <td>$alias</td>
            <td>$email</td>
            <td>$passwd</td>
            <td>$avatarURI</td>
            <td></td>
            </tr>";
        } else {
            echo "<tr name=$codUser>
            <th scope='row'>$codUser</th>
            <td>$firstName</td>
            <td>$lastName</td>
            <td>$alias</td>
            <td>$email</td>
            <td>$passwd</td>
            <td>$avatarURI</td>
            $button
        </tr>";
        }
    }
    echo '</tbody>
    </table>';
    ?>
    <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=chatapp" class="btn btn-outline-primary" role="button" aria-pressed="true">Manage Database</a>
    <a class="btn btn-primary" href="../logout.php" role="button">Logout</a>
</body>

</html>