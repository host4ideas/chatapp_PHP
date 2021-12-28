<?php
include_once '../db.php';
$errors = [];

if (isset($_GET['newuser']) && isset($_GET['id'])) {
    // Get the actionTime to check if the user has confirmed the register within the permitted hour
    $id = $_GET['id'];
    $sql_str = "SELECT actionTime, email from users where codUser like '$id'";
    $resul = $db->query($sql_str);
    $arrResul = $resul->fetch();
    $registerTime = $arrResul[0];
    $email = $arrResul[1];
    if ($arrResul != false) {
        $currentTime = date('Y-m-d H:i:s');
        $maxAllowedTime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($registerTime)));
        if ($currentTime > $maxAllowedTime) {
            $sql_str = "DELETE FROM users WHERE codUser like '$id'";
            $db->query($sql_str);
            $errors[] = "User with email: $email, has not been activated within the maximum time allowed. Please, register the user again.";
        } else {
            $sql_srt = "UPDATE users SET activated = '1' WHERE codUser like '$id'";
            $db->query($sql_srt);
            $errors[] = "User successfully registered, you can now login";
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['login'])) {
        $errors[] = "login cannot be empty";
    } else if (empty($_POST['passw'])) {
        $errors[] = "password cannot be empty";
    } else {
        $alias = htmlspecialchars($_POST['login']);
        $email = htmlspecialchars($_POST['login']);
        $passw1 = htmlspecialchars($_POST['passw']);
        try {
            $resultAlias = check_user($alias, $passw1, $db);
            $resultEmail = check_user(" ", $passw1, $db, $email);
            if ($resultAlias == "1" || $resultEmail == "1") {
                session_start();
                if ($resultAlias == "1") {
                    $_SESSION['codUser'] = $resultAlias;
                } else if ($resultEmail == "1") {
                    $_SESSION['codUser'] = $resultEmail;
                }
                header("Location: ./admin_zone.php");
            } else if ($resultAlias !== null && $resultAlias !== false) {
                session_start();
                $_SESSION['codUser'] = $resultAlias;
                header("Location: chats_home.php?id=" . $_SESSION['codUser']);
            } else if ($resultEmail !== null && $resultEmail !== false) {
                session_start();
                $_SESSION['codUser'] = $resultEmail;
                header("Location: chats_home.php?id=" . $_SESSION['codUser']);
            } else {
                $errors[] = "user not found";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " .  $e->getMessage();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/login.css">
    <title>Login Page</title>
</head>

<body>
    <div class="wrapper fadeInDown">
        <div id="formContent">
            <!-- Tabs Titles -->

            <!-- Icon -->
            <div class="first">
                <i class="far fa-user"></i>
            </div>

            <!-- Login Form -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <input <?php if (isset($erAlias) && $erAlias) echo "class = 'fadeIn second error'"; ?> type="text" id="login" class="fadeIn second" name="login" placeholder="alias or email" autofocus>

                <input <?php if (isset($erpassw) && $erpassw) echo "class = 'fadeIn second error'"; ?> type="password" id="passw" class="fadeIn second" name="passw" placeholder="password">

                <div class="wrap">
                    <button class="button">Submit</button>
                </div>
            </form>
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
            <!-- Remind Password -->
            <div id="formFooter">
                <a class="underlineHover" href="./recovery_mail_form.php">Forgot password?</a>
                <br><br>
                <a class="underlineHover" href="./login_new.php">Not yet a user?</a>
            </div>
        </div>
    </div>
</body>

</html>