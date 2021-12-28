<?php
session_start();
if (!isset($_SESSION['codUser'])) {
    header("Location: ../logout.php");
}
require_once '../functions.php';
require_once '../db.php';
$erAlias = $ermail = $erFile = false;
$alias = $mail = $fileURI = "";
$evOK = true;
$errors = [];
$codOtherUser = "";
$codMyUser = $_SESSION['codUser'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['otherUser'])) {
        $errors[] = "Enter an alias or a email";
        $erAlias = true;
        $ermail = true;
    } else {
        // Try with alias
        $alias = htmlspecialchars($_POST['otherUser']);
        $sql_str = "SELECT codUser from users where alias like '$alias'";
        $resul = $db->query($sql_str);
        if ($resul->rowCount() < 1) {
            // If with alias fails, try with email
            $mail = htmlspecialchars($_POST['otherUser']);
            $erAlias = true;
            $sql_str = "SELECT codUser from users where email like '$mail'";
            $resul = $db->query($sql_str);
            if ($resul->rowCount() < 1) {
                $errors[] = "No user found";
                $ermail = true;
            } else {
                $codOtherUser = $resul->fetch()[0];
            }
        } else {
            $codOtherUser = $resul->fetch()[0];
        }
    }
    // If at least the is one with no error
    if (!$ermail || !$erAlias) {
        $currentDate = date('Y-m-d H:i:s');

        var_dump($codOtherUser);
        var_dump($codMyUser);
        // As the shown image in chats is the image of the other user, it doesn't matter what image to upload
        $query1 = "INSERT INTO chats VALUES (null, 'privatechat', '../uploads/chatsImage/default_chat_image/chat1.png')";
        $resul1 = $db->query($query1);
        // Get the id of the last inserted chat by this PDO
        $codChat = $db->lastInsertId();
        $query2 = "INSERT INTO participate VALUES ('$codMyUser', '$codChat', '$currentDate')";
        $resul2 = $db->query($query2);
        $query3 = "INSERT INTO participate VALUES ('$codOtherUser', '$codChat', '$currentDate')";
        $resul3 = $db->query($query3);
        if ($resul1 == false || $resul2 == false || $resul3 == false) {
            $errors[] = "Error creating chat, contact administrator";
            // In case $resul1 had inserted the chat
            $query1 = "DELETE FROM chats WHERE codChat like '$codChat'";
            $db->query($query1);
        } else {
            header("Location: chats_home.php?id=" . $_SESSION['codUser']);
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
    <link rel="stylesheet" href="../views/styles/login.css">
    <title>New Private Chat</title>
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
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">

                <input <?php if ($erAlias) echo "class = 'fadeIn second error'";
                        else echo "value= '$alias'" ?> type="text" id="alias" class="fadeIn second" name="otherUser" placeholder="Enter an alias or a email">

                <div class="wrap">
                    <button class="button">Create private chat</button>
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

            <div id="formFooter">
                <a class="underlineHover" href="./chats_home.php?id=" <?php echo $_SESSION['codUser'] ?>>Cancel</a>
            </div>
        </div>
    </div>
</body>

</html>