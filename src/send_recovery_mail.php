<?php

require dirname(__FILE__) . "../../../../vendor/autoload.php";
require_once '../db.php';

use PHPMailer\PHPMailer\PHPMailer;

function loadConfigMail($name, $schema)
{
    $config = new DOMDocument();
    $config->load($name);
    $res = $config->schemaValidate($schema);
    if ($res === FALSE) {
        throw new InvalidArgumentException("Check configuration file");
    }
    $data = simplexml_load_file($name);
    $username = $data->xpath("//username")[0];
    $password = $data->xpath("//password")[0];
    $data = array();
    $data[0] = $username;
    $data[1] = $password;
    return $data;
}

function sendMail($to,  $body,  $subject = "", $user, $passwd)
{
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "tls";
    $mail->Host       = "smtp.gmail.com";
    $mail->Port       = 587;
    $mail->Username   = $user;
    $mail->Password   = $passwd;
    $mail->SetFrom($user, 'ChatApp');
    $mail->Subject    = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($to);
    if (!$mail->Send()) {
        return $mail->ErrorInfo;
    } else {
        return true;
    }
}

$evOK = true;
$errors = [];
$mail = "";
$ermail = false;
$codUser = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['mail'])) {
        $errors[] = "Mail cannot be empty";
        $ermail = true;
        $evOK = false;
    } else {
        $mail = htmlspecialchars($_POST['mail']);
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email";
            $ermail = true;
            $evOK = false;
        } else {
            $sql_str = "SELECT codUser from users where email like '$mail'";
            $resul = $db->query($sql_str);
            $codUser = $resul->fetch()['codUser'];
            if ($resul->rowCount() !== 1) {
                $errors[] = "That email is not registered";
                $ermail = true;
                $evOK = false;
            }
        }
    }

    if ($evOK) {
        // Added remote server functionality
        if ($_SERVER['REMOTE_ADDR'] == "1::1" || $_SERVER['REMOTE_ADDR'] == "::1") {
            $url = "https://localhost/chatapp_PHP/src/views/change_password.php?id=$codUser";
        } else {
            $url = "https://myfirstphp.ddns.net/views/change_password.php?id=$codUser";
        }
        $firstPart = file_get_contents("../views/first_part_recovery_mail.html");
        $urlPart = "<!-- start copy -->
        <tr>
            <td align='left' bgcolor='#ffffff' style='padding: 24px; font-family: Source_Sans_Pro, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;'>
                <p style='margin: 0;'>Tap the button below to recover your account password. If you
                    did not request to recover your password, you can safely delete this email.</p>
            </td>
        </tr>
        <!-- end copy -->
        
        <!-- start button -->
        <tr>
            <td align='left' bgcolor='#ffffff'>
                <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                        <td align='center' bgcolor='#ffffff' style='padding: 12px;'>
                            <table border='0' cellpadding='0' cellspacing='0'>
                                <tr>
                                    <td align='center' bgcolor='#1a82e2' style='border-radius: 6px;'>
                                        <a href='$url' target='_blank' style='display: inline-block; padding: 16px 36px; font-family: Source_Sans_Pro, Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 6px;'>Do
                                            Change password</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- end button -->
        
        <!-- start copy -->
        <tr>
            <td align='left' bgcolor='#ffffff' style='padding: 24px; font-family: Source_Sans_Pro, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;'>
                <p style='margin: 0;'>If that does not work, copy and paste the following link in your
                    browser:</p>
                <p style='margin: 0;'><a href='$url' target='_blank'>$url</a></p>
            </td>
        </tr>
        <!-- end copy -->";
        $configData = loadConfigMail(CONFIG_DIR . "/mail_configuration.xml", CONFIG_DIR . "/mail_configuration.xsd");
        $secondPart = file_get_contents("../views/second_part_recovery_email.html");
        $body = $firstPart . $urlPart . $secondPart;
        sendMail($mail,  $body,  $subject = "Forgot password", $configData[0], $configData[1]);
        include('../views/email_sent_confirmation.html');
        die();
    }
}
