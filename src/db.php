<?php
function load_config($name, $schema)
{
    $config = new DOMDocument();
    $config->load($name);
    $res = $config->schemaValidate($schema);
    if ($res === FALSE) {
        throw new InvalidArgumentException("Check configuration file");
    }
    $data = simplexml_load_file($name);
    $host = $data->xpath("//host")[0];
    $dbname = $data->xpath("//dbname")[0];
    $user = $data->xpath("//user")[0];
    $password = $data->xpath("//password")[0];
    $data = array();
    $constr = "mysql:dbname=" . $dbname . ";host=" . $host;
    $data[0] = $constr;
    $data[1] = $user;
    $data[2] = $password;
    return $data;
}
function check_user($alias, $password, $db, $email = null)
{
    $ins = "SELECT codUser, passwd, activated from users where email like '$email'";
    $result = $db->query($ins);
    if ($result->rowCount() < 1) {
        $ins2 = "SELECT codUser, passwd, activated from users where alias like '$alias'";
        $resul = $db->query($ins2);
        if ($resul->rowCount() > 0) {
            $resul = $resul->fetch();
            if ($resul['activated'] == '1') {
                $codUser = $resul['codUser'];
                $hashedPass = $resul['passwd'];
                if (password_verify($password, $hashedPass)) {
                    return $codUser;
                } else {
                    return false;
                }
            }
        }
    } else {
        // The string values TRUE and FALSE can be converted to bit values: TRUE is converted to 1 and FALSE is converted to 0.
        $result = $result->fetch();
        if ($result['activated'] == '1') {
            $hashedPass = $result['passwd'];
            $codUser = $result['codUser'];
            if (password_verify($password, $hashedPass)) {
                return $codUser;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
// When a user enters a chat, the dateEnter will be updated with the current date from table participate,
// that way you can check the difference in time between the user have entered the chat
// and the date when a meesage was send to check if it has been read or not
function updateDateEnter($codChat, $codUser, $db)
{
    $currentDate = date('Y-m-d H:i:s');
    $sql_srt = "UPDATE participate SET dateEnter = '$currentDate' WHERE codChat like '$codChat' and codUser like '$codUser'";
    $db->query($sql_srt);
}

try {
    define("UPLOADS_DIR", __DIR__ . '/uploads/');
    define("VIEWS_DIR", __DIR__ . '/views/');
    define("CONFIG_DIR", __DIR__ . '/config/');
    $connection_data = load_config(CONFIG_DIR . "/configuration.xml", CONFIG_DIR . "/configuration.xsd");
    $db = new PDO($connection_data[0], $connection_data[1], $connection_data[2]);
} catch (PDOException $e) {
    echo "Database error: " .  $e->getMessage();
}
