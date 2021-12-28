<?php
function chatsData($codUser, $actualChat, $db)
{
    try {
        $codUser = $_SESSION['codUser'];
        // Select all chats
        $query = "SELECT codChat FROM participate WHERE codUser like '$codUser'";
        $resul = $db->query($query);
        $chatsArray = array();
        $arrayResult = $resul->fetchAll();
        foreach ($arrayResult as $chatCode) {
            $chatArr = array();
            $codChat = $chatCode['codChat'];
            $chatArr[] = $codChat;

            // Select the image and name of each chat
            $query = "SELECT chatName, chatImageURI FROM chats WHERE codChat like '$codChat'";
            $resul = $db->query($query);
            $chatArr[] = $resul->fetch(PDO::FETCH_ASSOC);

            // As this are private chats (chats of two people), the name of the chat for each
            // participant user will be the name of the other user in the chat
            if ($chatArr[1]['chatName'] == "privatechat") {
                $query = "SELECT codUser FROM `participate` WHERE codChat like '$codChat' and codUser not like '$codUser'";
                $result = $db->query($query);
                $result = $result->fetch(PDO::FETCH_ASSOC);
                if ($result == false) {
                    $privateChatName = "Deleted user";
                    $chatArr[1]['chatName'] = $privateChatName;
                    $chatArr[1]["chatImageURI"] = "./uploads/avatars/default/default_avatar.jpg";
                } else {
                    $userCode = $result['codUser'];
                    $sql_str = "SELECT firstName, lastName, alias, avatarURI from users where codUser like '$userCode'";
                    $resul = $db->query($sql_str);
                    $resul = $resul->fetch(PDO::FETCH_ASSOC);
                    $privateChatName = $resul['firstName'] . " " . $resul['lastName'];
                    $chatArr[1]['chatName'] = $privateChatName;
                    $chatArr[1]["chatImageURI"] = $resul["avatarURI"];
                }
            }
            // Check last date the user entered a chat, if the last sent message was before the last time
            // the user entered, show the chat's text message as <b>Unread messages</b>
            $query = "SELECT dateEnter FROM participate WHERE codChat like '$codChat' and codUser like '$codUser'";
            $result = $db->query($query);
            $lastDateEnter = $result->fetch()[0];
            // Select last message from a chat and the date of that message
            $query = "SELECT dateSend FROM message WHERE codChat like '$codChat' ORDER BY codMg DESC LIMIT 1";
            $result = $db->query($query);
            $lastMessageDate = "";
            $lastTextMessage = "";
            if ($result->rowCount() > 0) {
                $lastMessageDate = $result->fetch()[0];
                $query = "SELECT textMessage FROM message WHERE codChat like '$codChat' ORDER BY codMg DESC LIMIT 1";
                $result = $db->query($query);
                $lastTextMessage = $result->fetch(PDO::FETCH_ASSOC);
            } else {
                $lastTextMessage = ['textMessage' => 'Post your first message'];
            }
            // If the user is in the same chat as the chat where the message arrived, print the message
            if ($actualChat != "undefined" && $lastDateEnter < $lastMessageDate && $codChat != $actualChat) {
                $lastTextMessage = ['textMessage' => '<b style="font-size: 0.70rem;">Unread messages</b>'];
            }

            $chatArr[] = $lastTextMessage;
            $chatsArray[] = $chatArr;
        }
        $json = json_encode($chatsArray, JSON_UNESCAPED_SLASHES);
        return $json;
    } catch (PDOException $e) {
        return "Database error: " .  $e->getMessage();
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
// Selects all messages from a chat
function chatMessages($codChat, $db)
{
    try {
        $query = "SELECT codMg FROM message WHERE codChat like '$codChat'";
        $resul = $db->query($query);
        $arrayResult = $resul->fetchAll();
        $arrMessages = array();
        foreach ($arrayResult as $codMessage) {
            $codMg = $codMessage['codMg'];
            $arrMessages[] = $codMg;
            // Select each message info
            $query = "SELECT * FROM message WHERE codMg like '$codMg'";
            $resul = $db->query($query);
            $arrMessages[] = $resul->fetch(PDO::FETCH_ASSOC);
        }
        $json = json_encode($arrMessages, JSON_UNESCAPED_SLASHES);
        return $json;
    } catch (PDOException $e) {
        return "Database error: " .  $e->getMessage();
    }
}
function getUserData($codUser, $db)
{
    try {
        $query = "SELECT avatarURI, email, alias FROM users WHERE codUser like '$codUser'";
        $resul = $db->query($query);
        $userData = $resul->fetch();
        return $userData;
    } catch (PDOException $e) {
        return "Database error: " .  $e->getMessage();
    }
}
