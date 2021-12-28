<?php
require '../send_recovery_mail.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Send recovery email</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <img src="https://scocre.com/assets/img/forgot.png" class="img-fluid" alt="">
            </div>
            <div class="col-md-9" style="padding-top:100px">
                <h2 class="font-weight-light">Forgot your password?</h2>
                Not to worry. Just enter your email address below and we'll send you an instruction email for recovery.
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="mt-3">
                    <input <?php if ($ermail) echo "class = 'form-control form-control-lg error'";
                            else echo "value= '$mail'" ?> type="email" id="mail" class="form-control form-control-lg" name="mail" placeholder="Enter your email">
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
                    <div class="text-right my-3">
                        <button type="submit" name="submit" class="btn btn-lg btn-success">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>