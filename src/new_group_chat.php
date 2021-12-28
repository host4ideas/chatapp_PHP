<?php
session_start();
if (!isset($_SESSION['codUser'])) {
    header("Location: ./logout.php");
}
require_once './functions.php';
require_once './db.php';
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
            $uploadResult = uploadFile($_FILES["chatImage"], "./uploads/chatsImage/", $image);
            if (!gettype($uploadResult[0]) == "string") {
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
            $strOtherUsers = str_replace(' ', '', htmlspecialchars($_POST['otherUsers']));
            $arrOtherUsers = explode(',', $strOtherUsers);
            // Iterate for each user input and check if the input is valid
            foreach ($arrOtherUsers as $otherUser) {
                // Try with alias for each result
                $tryAlias = $otherUser;
                $sql_str = "SELECT codUser from users where alias like '$tryAlias'";
                $resul = $db->query($sql_str);
                if ($resul->rowCount() < 1) {
                    $errors[] = "The user: $tryAlias doesn't exist";
                    // If not with alias try with email
                    $erAlias = true;
                    $tryEmail = $otherUser;
                    $sql_str = "SELECT codUser from users where email like '$tryEmail'";
                    $resul = $db->query($sql_str);
                    if ($resul->rowCount() < 1) {
                        // If not promp error
                        $errors[] = "The user: $tryEmail doesn't exist";
                        $query1 = "DELETE FROM chats WHERE codChat like '$codChat'";
                        $resul1 = $db->query($query1);
                        $evOK = false;
                    } else {
                        $codOtherUser = $resul->fetch()[0];
                        $query3 = "INSERT INTO participate VALUES ('$codOtherUser', '$codChat', '$currentDate')";
                        $resul3 = $db->query($query3);
                        $resul3 = $resul3->fetch();
                    }
                } else {
                    $codOtherUser = $resul->fetch()[0];
                    $query3 = "INSERT INTO participate VALUES ('$codOtherUser', '$codChat', '$currentDate')";
                    $resul3 = $db->query($query3);
                    $resul3 = $resul3->fetch();
                }
            }
            if ($evOK) {
                header("Location: ../views/chats_home.php?id=" . $_SESSION['codUser']);
            }
        }
    }
}
