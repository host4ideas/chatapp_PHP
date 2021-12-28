<?php
require_once '../process_change_password.php';
$id = "";
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/change_password.css">
    <title>Change password</title>
</head>

<body>
    <div class="container bootstrap snippets bootdey">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-2">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <input type="text" value="<?php echo $id ?>" name="id" style="display:none">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <span class="glyphicon glyphicon-th"></span>
                                Change password
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 separator social-login-box"> <br>
                                    <img alt="" class="img-thumbnail" src="https://bootdey.com/img/Content/avatar/avatar1.png">
                                </div>
                                <div style="margin-top:80px;" class="col-xs-6 col-sm-6 col-md-6 login-box">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
                                            <input <?php if ($erpassw) echo "class = 'form-control error'"; ?>class="form-control" type="password" id="passw" name="passw" placeholder="New password">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="glyphicon glyphicon-log-in"></span></div>
                                            <input <?php if ($erpassw) echo "class = 'form-control error'"; ?> class="form-control" type="password" id="passw2" name="passw2" placeholder="Repeat password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                        <div class="panel-footer">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6"></div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <button class="btn icon-btn-save btn-success" type="submit">
                                        <span class="btn-save-label"><i class="glyphicon glyphicon-floppy-disk"></i></span>save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>