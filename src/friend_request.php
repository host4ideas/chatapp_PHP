<?php
$currentDate = date('Y-m-d H:i:s');
if (isset($_GET['accepted']) && isset($_GET['id']) && isset($_GET['fr'])) {
    require_once './db.php';
    session_start();
    if (!isset($_SESSION['codUser'])) {
        header("Location: ../logout.php");
    }
    $codMyUser = $_SESSION['codUser'];
    $codFriend = htmlspecialchars($_GET['id']);
    $codFr = htmlspecialchars($_GET['fr']);
    $accepted = htmlspecialchars($_GET['accepted']);
    $codChat = htmlspecialchars($_GET['chat']);
    // Update the FR status and add the friend to the user
    if ($_GET['accepted'] == "true") {
        $sql_srt = "UPDATE friendrequest SET accepted = 1 WHERE codFr like '$codFr'";
        $db->query($sql_srt);
        $query1 = "INSERT INTO friends VALUES ('$codMyUser', '$codFriend', '$currentDate')";
        $db->query($query1);
        $query1 = "INSERT INTO friends VALUES ('$codFriend', '$codMyUser', '$currentDate')";
        $db->query($query1);
        $query1 = "DELETE FROM friendrequest WHERE codFr like '$codFr'";
        $db->query($query1);
        $query1 = "DELETE FROM chats WHERE codChat like '$codChat'";
        $db->query($query1);
        header('Location: ./views/friend_list.php');
    } else {
        $query1 = "DELETE FROM friendrequest WHERE codFr like '$codFr'";
        $db->query($query1);
        $query1 = "DELETE FROM chats WHERE codChat like '$codChat'";
        $db->query($query1);
        header("Location: ./views/chats_home.php?id=$codMyUser");
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once '../functions.php';
    require_once '../db.php';
    $codMyUser = $codUser;
    $erAlias = $ermail = false;
    $alias = $mail = "";
    $evOK = true;
    $errors = [];
    $codOtherUser = "";
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

        // Check if both users are already friends
        $sql_str1 = "SELECT codUser from friends where codUser like '$codMyUser' and codFriend like '$codOtherUser'";
        $resul1 = $db->query($sql_str1);
        if ($resul1->rowCount() > 0) {
            $errors[] = "You are already friends";
            header('Location: ./friend_list.php');
            die();
        }

        // To send a friend request first we need a new private chat
        $query1 = "INSERT INTO chats VALUES (null, 'Friend request', './uploads/chatsImage/default_chat_image/friend_request.jpg')";
        $resul1 = $db->query($query1);
        // Get the id of the last inserted row
        $codChat = $db->lastInsertId();
        // In that chat we only have to insert the other user
        $query3 = "INSERT INTO participate VALUES ('$codOtherUser', '$codChat', '$currentDate')";
        $resul3 = $db->query($query3);
        if (!$resul1 || !$resul3) {
            $errors[] = "Error creating chat, contact administrator";
            // In case $resul1 has inserted the chat
            $query1 = "DELETE FROM chats WHERE codChat like '$codChat'";
            $db->query($query1);
        } else {
            // If all goes OK, insert the friend request and send the message of friend request
            $query1 = "INSERT INTO friendrequest VALUES (null, '$currentDate', 0, '$codOtherUser', '$codChat')";
            $resul1 = $db->query($query1);
            $codFr = $db->lastInsertId();
            $sql_str = "SELECT alias from users where codUser like '$codMyUser'";
            $resul = $db->query($sql_str);
            $myAlias = $resul->fetch()[0];
            $message = '<a class="btn btn-primary" href="../friend_request.php?id=' . "$codMyUser" . '&fr=' . "$codFr" . '&accepted=true&chat=' . "$codChat" . '" role="button">Accept</a><a class="btn btn-danger" href="../friend_request.php?id=' . "$codMyUser" . '&fr=' . "$codFr" . '&accepted=false&chat=' . "$codChat" . '" role="button">Reject</a>';
            $query1 = "INSERT into message VALUES (null, '$currentDate', null, '$codMyUser', '$codChat', '$message', '$myAlias')";
            $resul1 = $db->query($query1);
        }
    }
}
