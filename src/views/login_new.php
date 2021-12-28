<?php
require_once '../process_register.php';
require_once '../functions.php';
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
    <title>Sign Up Page</title>
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
                <input <?php if ($erFirstName) echo "class = 'fadeIn second error'";
                        else echo "value= '$firstName'" ?> type="text" id="firstName" class="fadeIn second" name="firstName" placeholder="Enter your name" autofocus>

                <input <?php if ($erLastName) echo "class = 'fadeIn second error'";
                        else echo "value= '$lastName'" ?> type="text" id="lastName" class="fadeIn second" name="lastName" placeholder="Enter your surname">

                <input <?php if ($erAlias) echo "class = 'fadeIn second error'";
                        else echo "value= '$alias'" ?> type="text" id="alias" class="fadeIn second" name="alias" placeholder="Enter an alias">

                <input <?php if ($ermail) echo "class = 'fadeIn second error'";
                        else echo "value= '$mail'" ?> type="email" id="mail" class="fadeIn second" name="mail" placeholder="Enter your email">

                <input <?php if ($erpassw) echo "class = 'fadeIn second error'"; ?> type="password" id="passw" class="fadeIn second" name="passw" placeholder="Enter password">

                <input <?php if ($erpassw) echo "class = 'fadeIn second error'"; ?> type="password" id="passw2" class="fadeIn second" name="passw2" placeholder="Repeat password">

                <input <?php if ($erAvatarURI) echo "class = 'fadeIn second error'"; ?> type="file" id="avatar" class="fadeIn second" name="avatar" placeholder="Upload your avatar">
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
                    <button class="button">Submit</button>
                </div>
            </form>

            <div id="formFooter">
                <a class="underlineHover" href="../views/login_already.php">Already have an account?</a>
            </div>
        </div>
    </div>
</body>

</html>