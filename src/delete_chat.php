<?php
require_once '../db.php';


$sql_str = "SELECT chatName from chats where codChat like '$deleteChat'";
$resul = $db->query($sql_str);
$chatName = $resul->fetch()[0];
// If it's a friend request, we need first to delete the friendrequest before deleting the chat
if ($chatName == "Friend request") {
    $sql_str = "DELETE FROM friendrequest WHERE codChat like '$deleteChat'";
    $resul = $db->query($sql_str);
    $sql_str = "DELETE FROM chats WHERE codChat like '$deleteChat'";
    $resul = $db->query($sql_str);
    header("Location: ../views/chats_home.php?id=" . $_COOKIE['codUser']);
} else {
    $sql_str = "DELETE FROM chats WHERE codChat like '$deleteChat'";
    $resul = $db->query($sql_str);
    header("Location: ../views/chats_home.php?id=" . $_COOKIE['codUser']);
}
