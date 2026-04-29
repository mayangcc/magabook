<?php 

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = require '../config/mail.php';

function  sendEmail($toEmail, $toName, $subject, $body) {
    global $config;

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port       = $config['port'];

        //Recipients  the from_email needs to be changed to the email address that you will be using to send the emails, otherwise it will not work
        $mail->setFrom($config['from_email'], 'Magabook');
        $mail->addAddress($toEmail, $toName);

        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;

    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}
?>