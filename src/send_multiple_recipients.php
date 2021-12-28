<?php
require_once './functions.php';
require_once './db.php';
session_start();
if (!isset($_SESSION['codUser'])) {
    header("Location: ./logout.php");
}
$codMyUser = $_SESSION['codUser'];
$query = "SELECT alias FROM users WHERE codUser like '$codMyUser'";
$result = $db->query($query);
$myAlias = $result->fetch()[0];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['groupParticipants'])) {
        $usersToSend = $_POST['groupParticipants'];
        $currentDate = date('Y-m-d H:i:s');
        $message = htmlspecialchars($_POST['messageText']);

        if ($_FILES["broadcastImage"]['size'] > 0) {
            $uploadResult = uploadFile($_FILES["broadcastImage"], "./uploads/attachments/", true);
            if (gettype($uploadResult) == "array") {
                $uploadResult = uploadFile($_FILES["broadcastImage"], "./uploads/attachments/", false);
            }
            $uploadResult = "." . $uploadResult;

            foreach ($usersToSend as $codOtherUser) {
                $query1 = "INSERT INTO chats VALUES (null, 'privatechat', '../uploads/chatsImage/default_chat_image/chat1.png')";
                $resul1 = $db->query($query1);
                $resul1 = $resul1->fetch();
                $codChat = $db->lastInsertId();
                $query2 = "INSERT INTO participate VALUES ('$codMyUser', '$codChat', '$currentDate')";
                $resul2 = $db->query($query2);
                $query2 = "INSERT INTO participate VALUES ('$codOtherUser', '$codChat', '$currentDate')";
                $resul2 = $db->query($query2);
                $query3 = "INSERT into message VALUES (null, '$currentDate', '$uploadResult', '$codMyUser', '$codChat', '$message', '$myAlias')";
                $db->query($query3);
            }
        } else {
            foreach ($usersToSend as $codOtherUser) {
                $query1 = "INSERT INTO chats VALUES (null, 'privatechat', '../uploads/chatsImage/default_chat_image/chat1.png')";
                $resul1 = $db->query($query1);
                $resul1 = $resul1->fetch();
                $codChat = $db->lastInsertId();
                $query2 = "INSERT INTO participate VALUES ('$codMyUser', '$codChat', '$currentDate')";
                $resul2 = $db->query($query2);
                $query2 = "INSERT INTO participate VALUES ('$codOtherUser', '$codChat', '$currentDate')";
                $resul2 = $db->query($query2);
                $query3 = "INSERT into message VALUES (null, '$currentDate', null, '$codMyUser', '$codChat', '$message', '$myAlias')";
                $db->query($query3);
            }
        }
    }
    $url = "./views/chats_home.php?id=$codMyUser";
    header("Location: $url");
}
