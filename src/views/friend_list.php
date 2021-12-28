<?php
session_start();
if (!isset($_SESSION['codUser'])) {
    header("Location: ../logout.php");
}
$codUser = $_SESSION['codUser'];
$errors = array();
require_once '../db.php';
require_once '../friend_request.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <title>Friend List</title>
</head>

<body>
    <?php
    $ins = "SELECT dateFriend, codFriend from friends WHERE codUser like '$codUser'";
    $resul = $db->query($ins);
    $resul = $resul->fetchAll(PDO::FETCH_ASSOC);

    echo '
    <form action="./new_chat.php" method="POST">
    <table class="table table-dark">
    <thead>
        <tr>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Alias</th>
        <th scope="col">Email</th>
        <th scope="col">Avatar</th>
        <th scope="col">Friends since</th>
        <th scope="col">Create chat</th>
        </tr>
    </thead>
    <tbody>';

    foreach ($resul as $friendRow) {
        $dateFriend = $friendRow['dateFriend'];
        $codOtherUser = $friendRow['codFriend'];

        $ins = "SELECT firstName, lastName, alias, email, avatarURI from users WHERE codUser like '$codOtherUser'";
        $resul = $db->query($ins);
        $resul = $resul->fetch(PDO::FETCH_ASSOC);

        $firstName = $resul['firstName'];
        $lastName = $resul['lastName'];
        $alias = $resul['alias'];
        $email = $resul['email'];
        $avatarURI = "." . $resul['avatarURI'];

        echo "<input type='text' name='otherUser' value='$alias' style='display: none;'>
        <tr name=$alias>
            <td>$firstName</td>
            <td>$lastName</td>
            <td>$alias</td>
            <td>$email</td>
            <td><img src='$avatarURI' width='100px' height='100px'/></td>
            <td>$dateFriend</td>
            <td><button type='submit' class='btn btn-primary'>Chat</button></td>
        </tr>";
    }
    echo '</tbody>
    </table>
    </form>';
    ?>
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
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        New friend
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New friend request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="form-group m-5">
                        <label for="inputAliasEmail">Alias or email address</label>
                        <input type="text" name="otherUser" class="form-control" id="inputAliasEmail" aria-describedby="emailHelp">
                        <small id="emailHelp" class="form-text text-muted">Enter a user to send a friend
                            request</small>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Send friend request</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <a type="button" href="./chats_home.php?id=<?php echo $codUser ?>" class="btn btn-danger" data-dismiss="modal">Return</a>
</body>

</html>