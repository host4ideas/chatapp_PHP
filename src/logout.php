<?php
setcookie('codUser', " ", time() - 3600, "/");
setcookie("codChat", " ", time() - 3600, "/");
session_start();
$_SESSION = array();
session_destroy();
setcookie(session_name(), 123, time() - 3600);
header("Location:./views/login_already.php");
