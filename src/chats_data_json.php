<?php
require_once './db.php';
require './home_data.php';
session_start();
echo chatsData($_SESSION['codUser'], $_GET['chat'], $db);
