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
$strOtherUsers = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['chatName'])) {
        $errors[] = "Enter an alias or a email";
        $erAlias = true;
        $ermail = true;
        $evOK = false;
    } else {
        $chatName = $_POST['chatName'];
    }
    if (empty($_POST['otherUsers'])) {
        $errors[] = "Enter an alias or a email";
        $erAlias = true;
        $ermail = true;
        $evOK = false;
    } else {
        // Upload group image
        if ($_FILES["chatImage"]['size'] > 0) {
            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["chatImage"]["tmp_name"]);
            if ($check === false) {
                $image = false;
            } else {
                $image = true;
            }
            $uploadResult = uploadFile($_FILES["chatImage"], "../uploads/chatsImage/", $image);
            if (gettype($uploadResult[0]) == "string") {
                $chatImage = str_replace('..', '.', $uploadResult);
            } else {
                $chatImage = './uploads/chatsImage/default_chat_image/chat1.png';
            }
        } else {
            $chatImage = './uploads/chatsImage/default_chat_image/chat1.png';
        }
        if ($evOK) {
            // Create the chat to retrieve the codChatis something gone wrong this will be deleted
            $query1 = "INSERT INTO chats VALUES (null, '$chatName', '$chatImage')";
            $resul1 = $db->query($query1);
            // Get the id of the last inserted chat by this PDO
            $codChat = $db->lastInsertId();
            // Get the id of the current user
            $codMyUser = $_SESSION['codUser'];

            // Insert the current user into the participate table, is something gone wrong this relation will be deleted when deleting the chat
            $currentDate = date('Y-m-d H:i:s');
            $query2 = "INSERT INTO participate VALUES ('$codMyUser', '$codChat', '$currentDate')";
            $resul2 = $db->query($query2);
            $resul2 = $resul2->fetch();

            // Get all alias and emails from the input
            $arrOtherUsers = $_POST['otherUsers'];
            // Iterate for each user input and check if the input is valid
            foreach ($arrOtherUsers as $codOtherUser) {
                // In case the input is modified and some one inserts the admin id
                if ($codOtherUser != 1) {
                    $query3 = "INSERT INTO participate VALUES ('$codOtherUser', '$codChat', '$currentDate')";
                    $resul3 = $db->query($query3);
                    $resul3 = $resul3->fetch();
                }
            }
            if ($evOK) {
                header("Location: chats_home.php?id=" . $_SESSION['codUser']);
            }
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
    <title>New Group Chat</title>
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

                <label for="groupParticipants">Select users to create the group</label>
                <select class="form-select" multiple aria-label="multiple select example" name="otherUsers[]" id="groupParticipants">
                    <?php
                    $query = "SELECT alias, codUser FROM users";
                    $result = $db->query($query);
                    $users = $result->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($users as $user) {
                        $alias = $user['alias'];
                        $cod = $user['codUser'];
                        if ($alias != "admin" && $cod != $_SESSION['codUser']) {
                            echo "<option value='$cod'>$alias</option>";
                        }
                    }
                    ?>
                </select>

                <input type="text" id="chatName" class="fadeIn second" name="chatName" placeholder="Enter the group name">

                <!-- <input <?php if ($erAlias) echo "class = 'fadeIn second error'";
                            else echo "value= '$alias'" ?> type="text" id="alias" class="fadeIn second" name="otherUsers" placeholder="Enter alias or emails separated by commas"> -->

                <input <?php if ($erFile) echo "class = 'fadeIn second error'"; ?> type="file" id="chatImage" class="fadeIn second" name="chatImage" placeholder="Upload your avatar">

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

                <div class="wrap">
                    <button class="button">Create group chat</button>
                </div>
            </form>

            <div id="formFooter">
                <a class="underlineHover" href="./chats_home.php?id=" <?php echo $_SESSION['codUser'] ?>>Cancel</a>
            </div>
        </div>
    </div>
</body>

</html>