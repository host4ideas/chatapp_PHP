<?php
require_once './home_data.php';
require_once './db.php';
echo chatMessages($_GET['chat'], $db);
