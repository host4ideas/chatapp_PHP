<?php
require_once './db.php';
require "../vendor/autoload.php";

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


$codUser = $_GET['id'];
$mail = $_GET['mail'];
// Added remote server functionality
if ($_SERVER['REMOTE_ADDR'] == "1::1" || $_SERVER['REMOTE_ADDR'] == "::1") {
    $url = "https://localhost/chatapp_PHP/src/views/login_already.php?newuser=true&id=$codUser";
} else {
    $url = "https://myfirstphp.ddns.net/views/login_already.php?newuser=true&id=$codUser";
}
$firstPart = file_get_contents(VIEWS_DIR . "first_part_registration_email.html");
$urlPart = "<!-- start copy -->
        <tr>
            <td align='left' bgcolor='#ffffff' style='padding: 24px; font-family: Source_Sans_Pro, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;'>
                <p style='margin: 0;'>Tap the button below to confirm your registration and login to the platform.</p>
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
                                        <a href='$url' style='display: inline-block; padding: 16px 36px; font-family: Source_Sans_Pro, Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 6px;'>Confirm register</a>
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
                <p style='margin: 0;'><a href='$url'>$url</a></p>
            </td>
        </tr>
        <!-- end copy -->";
$configData = loadConfigMail(CONFIG_DIR . "/mail_configuration.xml", CONFIG_DIR . "/mail_configuration.xsd");
$secondPart = file_get_contents(VIEWS_DIR . "second_part_registration_email.html");
$body = $firstPart . $urlPart . $secondPart;
sendMail($mail,  $body,  $subject = "Confirmation Email", $configData[0], $configData[1]);
include(VIEWS_DIR . 'email_sent_confirmation.html');
die();
