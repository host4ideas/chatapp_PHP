<?php
session_start();
if (!isset($_SESSION['codUser'])) {
    header("Location: ../logout.php");
}
require_once '../home_data.php';
require_once '../db.php';
require_once '../functions.php';
// Get image, email and alias from the table users
$userImageEmailAlias = getUserData($_SESSION['codUser'], $db);
$alias = $userImageEmailAlias[2];
setcookie('codUser', $_SESSION['codUser'], time() + 3600, "/");
// We set cookies used for passing info to AJAX
if (isset($_GET['chat']) && isset($_GET['delete'])) {
    $deleteChat = $_GET['chat'];
    setcookie('codChat', $_GET['chat'], time() + 600, "/");
    require_once '../delete_chat.php';
} else if (isset($_GET['chat'])) {
    // Update first the previous entered chat (if a chat was pressed previously)
    if (isset($_COOKIE['codChat'])) {
        updateDateEnter($_COOKIE['codChat'], $_SESSION['codUser'], $db);
    }
    // Then update the new clicked chat
    updateDateEnter($_GET['chat'], $_SESSION['codUser'], $db);
    setcookie('codChat', $_GET['chat'], time() + 600, "/");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();
    try {
        $codChat = $_COOKIE['codChat'];
    } catch (Exception $e) {
        $errors[] = "Please, press the logout button and login again";
    }
    $codUser = $_SESSION['codUser'];
    $currentDate = date('Y-m-d H:i:s');
    $messageText = $_POST['messageText'];
    if ($_FILES["messageFile"]['size'] > 0) {
        $messageFile = $_FILES["messageFile"];
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["messageFile"]["tmp_name"]);
        if ($check === false) {
            $image = false;
        } else {
            $image = true;
        }
        $uploadResult = uploadFile($messageFile, "../uploads/attachments/", $image);
        if ($uploadResult[0] !== ".") {
            $errors = $uploadResult;
        } else {
            $query = "INSERT into message VALUES (null, '$currentDate', '$uploadResult', '$codUser', '$codChat', '$messageText', '$alias')";
            $resul = $db->query($query);
        }
    } else {
        $query = "INSERT into message VALUES (null, '$currentDate', null, '$codUser', '$codChat', '$messageText', '$alias')";
        $resul = $db->query($query);
        if ($resul == false) {
            $errors[] = "Error sending message, contact administrator";
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
    <link rel="stylesheet" href="./styles/home.css">
    <title>Home</title>
</head>

<body>
    <div class="fluid-container">
        <div class="tile tile-alt" id="messages-main">
            <div class="ms-body">
                <div class="action-header clearfix">
                    <!-- Load the chats names -->
                    <div id="chatInfo" class="pull-left hidden-xs">
                    </div>
                </div>
                <!-- 
                    Load the chat's messages
                    Pull left the responses
                    Pull right your messages
                -->
                <div class="messages" id="messages-feed">
                    <a id="autoScrollTop" href="#first-message"></a>
                    <div id="last-message"></div>
                    <a id="autoScrollBottom" href="#last-message"></a>
                    <div id="last-message"></div>
                </div>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                    <div class="msb-reply" id="replyArea">
                        <textarea placeholder="What's on your mind...(max. 400 characters)" name="messageText" maxlength="400"></textarea>
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Upload a file (image or PDF)</label>
                            <input class="form-control-file" type="file" name="messageFile">
                        </div>
                        <button name="submit"><i class="far fa-paper-plane"></i></button>
                    </div>
                </form>
            </div>
            <div class="ms-menu">
                <div class="ms-user clearfix">
                    <!-- 
                        Load user's avatar and email
                     -->
                    <img src=<?php echo "." . ($userImageEmailAlias[0]) ?> alt="" class="img-avatar pull-left">
                    <div>Signed in as <br> <?php echo $userImageEmailAlias[1] ?></div>
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
                </div>
                <div>
                    <div class="list-group lg-alt" id="listChats">
                        <!-- 
                        Load each chat the user belongs to
                     -->
                    </div>
                </div>
                <div id="userActions">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Chats options
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="./new_chat.php" role="button">New chat</a></li>
                            <li><a class="dropdown-item" href="./new_group_chat.php" role="button">New group</a></li>
                            <li>
                                <!-- Button trigger modal -->
                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Broadcast list
                                </button>
                            </li>
                        </ul>
                    </div>
                    <!-- Modal broadcast list -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">New broadcast list</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="../send_multiple_recipients.php" method="POST" enctype="multipart/form-data">
                                    <div class="form-group m-2">
                                        <!-- Every available user -->
                                        <select class="form-select m-1" multiple aria-label="multiple select example" name="groupParticipants[]" id="groupParticipants">
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
                                        <textarea placeholder="What's on your mind...(max. 400 characters)" name="messageText" maxlength="400" class="m-1"></textarea>
                                        <input type="file" name="broadcastImage">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Send</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-primary" href="./friend_list.php" role="button">Friends</a>
                    <a class="btn btn-success" href="./modify_profile.php?id=<?php echo $_SESSION['codUser'] ?>" role="button">Profile</a>
                    <a class="btn btn-warning" href="../logout.php" type="button">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    </script>
    <script>
        function getExtension(filename) {
            var parts = filename.split('.');
            return parts[parts.length - 1];
        }

        function isImage(filename) {
            var ext = getExtension(filename);
            switch (ext.toLowerCase()) {
                case 'jpg':
                case 'jpeg':
                case 'jfif':
                case 'pjpeg':
                case 'pjp':
                case 'gif':
                case 'png':
                case 'svg':
                case 'webp':
                    return true;
            }
            return false;
        }

        function isDoc(filename) {
            var ext = getExtension(filename);
            switch (ext.toLowerCase()) {
                case 'pdf':
                    return true;
            }
            return false;
        }

        function check_cookie_name(name) {
            var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            if (match) {
                return match[2];
            }
        }
        /*
        Load and create each element chat
        */
        function loadMessages() {
            var codChat = check_cookie_name("codChat");
            // If the cookie is undefined, means that the user isn't in a chat, so it's not necessary to update the messages
            if (codChat != undefined) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    // Parse the JSON file from
                    if (this.readyState == 4 && this.status == 200) {
                        // Parse the JSON file from
                        console.log(this.response);
                        const DATA = JSON.parse(this.response);
                        // Some problems with JSON indexes, but is working
                        for (let i = 1; i <= DATA.length; i += 2) {
                            // Check if the last message is already added to the chat
                            if (!document.body.contains(document.getElementById("message" + (DATA.length - 1)))) {
                                var messagesBody = document.getElementById("messages-feed");
                                // message-feed right for your messages
                                if (DATA[i]['codUser'] == check_cookie_name("codUser")) {
                                    var containerMessage = document.createElement("div");
                                    containerMessage.setAttribute("class", "message-feed right");
                                    containerMessage.id = "message" + i;
                                    messagesBody.appendChild(containerMessage);
                                } else {
                                    // message-feed media for other's mesages
                                    var containerMessage = document.createElement("div");
                                    containerMessage.setAttribute("class", "message-feed media");
                                    containerMessage.id = "message" + i;
                                    messagesBody.appendChild(containerMessage);
                                }

                                var divMedia = document.createElement("div");
                                divMedia.setAttribute("class", "media-body");
                                containerMessage.appendChild(divMedia);

                                var content = document.createElement("div");
                                content.setAttribute("class", "mf-content");

                                var contentText = document.createElement("p");
                                contentText.innerHTML = DATA[i]['textMessage'];

                                // If the message contains a compatible file URI do the following:
                                if (DATA[i]['fileUri'] != null && isImage(DATA[i]['fileUri'])) {
                                    // Is is a path to a image, create a img element
                                    var imgAttachment = document.createElement("img");
                                    imgAttachment.setAttribute("src", DATA[i]['fileUri']);
                                    imgAttachment.style.width = "100px";
                                    content.appendChild(imgAttachment);
                                    contentText.style.position = "relative";
                                    contentText.style.top = "20px";
                                } else if (DATA[i]['fileUri'] != null && isDoc(DATA[i]['fileUri'])) {
                                    // Is is a path to a pdf, create a embed of type pdf
                                    var previewText = document.createElement("p");
                                    previewText.innerHTML = "Document Preview";
                                    content.appendChild(previewText);
                                    var pdfAttachment = document.createElement("embed");
                                    pdfAttachment.style.paddingRight = "11px";
                                    pdfAttachment.setAttribute("src", DATA[i]['fileUri']);
                                    pdfAttachment.setAttribute("type", "application/pdf");
                                    pdfAttachment.style.width = "400px";
                                    pdfAttachment.style.height = "400px";
                                    content.appendChild(pdfAttachment);
                                    var downloadLink = document.createElement("a");
                                    downloadLink.style.display = "block";
                                    downloadLink.setAttribute("href", DATA[i]['fileUri']);
                                    downloadLink.innerHTML = "View PDF";
                                    content.appendChild(downloadLink);
                                    contentText.style.position = "relative";
                                    contentText.style.top = "20px";
                                }

                                // Append to the bottom the message's text
                                content.appendChild(contentText);

                                // Append the message
                                divMedia.appendChild(content);

                                // Append the timestamp of the message
                                var sendDate = document.createElement("small");
                                sendDate.setAttribute("class", "mf-date");
                                sendDate.innerHTML = DATA[i]['dateSend'];
                                divMedia.appendChild(sendDate);

                                var dateFontawesome = document.createElement("i");
                                dateFontawesome.setAttribute("class", "fa fa-clock-o");
                                sendDate.appendChild(dateFontawesome);

                                // Append who sent the message
                                var aliasSender = document.createElement("small");
                                aliasSender.setAttribute("class", "mf-date");
                                if (DATA[i]['alias'] == null) {
                                    aliasSender.innerHTML = "Deleted user";
                                } else {
                                    aliasSender.innerHTML = DATA[i]['alias'];
                                }
                                divMedia.appendChild(aliasSender);

                                messagesBody.appendChild(containerMessage);
                            }
                        }
                    }
                };
                xhttp.open("GET", "../chat_messages_json.php?chat=" + codChat, true);
                xhttp.send();
                return false;
            }
        }
        loadMessages();
        setInterval(loadMessages, 1000);
        /*
            Load and create each element message
        */
        var count = 0;

        function loadChats() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Parse the JSON file from
                    const DATA = JSON.parse(this.response);
                    for (let i = 0; i < DATA.length; i++) {
                        // Check if the last chat is already added to the chat list
                        if (!document.body.contains(document.getElementById(DATA[i][0]))) {
                            var codChat = check_cookie_name("codChat");
                            // Check if the user actually is inside a chat
                            for (let j = 0; j < DATA.length; j++) {
                                if (DATA[j][0] == codChat) {
                                    while (count < 1) {
                                        var chatInfo = document.getElementById("chatInfo");
                                        // Chat image in the header of the chat
                                        var imgChatInChat = document.createElement("img");
                                        imgChatInChat.setAttribute("src", "." + DATA[j][1]['chatImageURI']);
                                        imgChatInChat.setAttribute("class", "img-avatar m-r-10");
                                        chatInfo.appendChild(imgChatInChat);

                                        // Chat name in the header of the chat
                                        var chatName3 = document.createElement("span");
                                        chatName3.innerHTML = DATA[j][1]['chatName'];
                                        chatInfo.appendChild(chatName3);
                                        count++;
                                    }
                                }
                            }

                            var chatsMenu = document.getElementById("listChats");
                            var chatItem = document.createElement("a");
                            chatItem.id = DATA[i][0];
                            chatItem.setAttribute("class", "list-group-item media");
                            chatItem.setAttribute("href", "./chats_home.php?chat=" + chatItem.id);
                            chatsMenu.appendChild(chatItem);

                            var imgChatContainer = document.createElement("div");
                            imgChatContainer.setAttribute("class", "pull-left");
                            imgChatContainer.style.marginRight = "20px";
                            chatItem.appendChild(imgChatContainer);

                            var imgChat = document.createElement("img");
                            // Load image from JSON - ERROR
                            imgChat.setAttribute("src", "." + DATA[i][1]['chatImageURI']);
                            imgChat.setAttribute("class", "img-avatar");
                            imgChatContainer.appendChild(imgChat);

                            var divMedia = document.createElement("div");
                            divMedia.setAttribute("class", "media-body");
                            chatItem.appendChild(divMedia);

                            var chatName2 = document.createElement("b");
                            chatName2.setAttribute("class", "list-group-item-heading");
                            chatName2.innerHTML = DATA[i][1]['chatName'] + "<br>";
                            divMedia.appendChild(chatName2);

                            var deleteButton = document.createElement("a");
                            deleteButton.setAttribute("class", "btn btn-outline-danger");
                            deleteButton.setAttribute("href", "./chats_home.php?chat=" + chatItem.id + "&delete=true");
                            deleteButton.setAttribute("role", "button");
                            deleteButton.innerHTML = "<i class='far fa-trash-alt'></i>";
                            deleteButton.style.width = "25px";
                            imgChatContainer.appendChild(deleteButton);

                            var lastMessage = document.createElement("small");
                            lastMessage.setAttribute("class", "list-group-item-text c-gray");
                            lastMessage.id = "lastmessage" + DATA[i][0];
                            // Last message from the chat, if it's not defined it's because it's a new chat
                            if (DATA[i][2] == "Unread Messages") {
                                lastMessage.innerHTML = "<b>Unread messages</b>";
                            } else {
                                lastMessage.innerHTML = DATA[i][2]['textMessage'];
                            }
                            divMedia.appendChild(lastMessage);
                        } else {
                            var lastMessage = document.getElementById("lastmessage" + DATA[i][0]);
                            if (DATA[i][2] == "Unread Messages") {
                                lastMessage.innerHTML = "<b>Unread messages</b>";
                            } else {
                                lastMessage.innerHTML = DATA[i][2]['textMessage'];
                            }
                        }
                    }
                }
            };
            var codChat = check_cookie_name("codChat");
            if (codChat == undefined) {
                codChat = "undefined";
            }
            xhttp.open("GET", "../chats_data_json.php?chat=" + codChat, true);
            xhttp.send();
            return false;
        }
        loadChats();
        setInterval(loadChats, 1000);
    </script>
</body>

</html>