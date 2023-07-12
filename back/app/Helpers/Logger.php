<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Carbon\Carbon;

class Logger
{
    public function log($date, $userID, $userName, $ControllerName, $functionName, $logType, $message)
    {
        $filenameDate = date("Ym");
        $myfile = fopen("logs/log" . $filenameDate . ".txt", "a") or die("Unable to open file!");
        $txt = $date . "\t--\t" . $userID . "\t--\t" . $userName . "\t--\t" . $ControllerName .
            "\t--\t" . $functionName . "\t--\t" . $logType . "\t--\t" . $message . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
        return "ok";
    }

    public function logError($date, $userID, $userName, $ControllerName, $functionName, $logType, $message)
    {
        $filenameDate = date("Ym");
        $myfile = fopen("logs/ErrorLog" . $filenameDate . ".txt", "a") or die("Unable to open file!");
        $txt = $date . "\t--\t" . $userID . "\t--\t" . $userName . "\t--\t" . $ControllerName .
            "\t--\t" . $functionName . "\t--\t" . $logType . "\t--\t" . $message . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
        return "ok";
    }

    public function mailer($subject, $message, $sender, $senderName, $sendTo, $CCTO)
    {
        $mail = new PHPMailer(true);
        // Server settings
        $mail->SMTPDebug = 0;
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();

        $mail->Host = 'mail.dctechmicro.com'; //'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mdmorta@dctechmicro.com';
        $mail->Password = 'incorrect';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 25;
        $mail->CharSet = 'utf-8';
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        //any suggestion about the sender??
        $mail->setFrom('mdmorta@dctechmicro.com', $senderName . " " . $sender);
        if ($sendTo != null)
            foreach ($sendTo as $item) {
                $item = (object) $item;
                $mail->addAddress($item->email, $item->name);
            }
        if ($CCTO != null)
            foreach ($CCTO as $item) {
                $item = (object) $item;
                $mail->addCC($item->email, $item->name);
            }

        //$mail->addReplyTo($request->email, 'Mailer');
        //$mail->addCC('pbismonte@dctechmicro.com');
        //$mail->addBCC('his-her-email@gmail.com');

        //Attachments (optional)
        // $mail->addAttachment('/var/tmp/file.tar.gz');			// Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');	// Optional name

        $mail->isHTML(true);                                                                     // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
    }

    public function mailerZimbra($subject, $message, $sender, $senderName, $sendTo, $CCTO)
    {
        $mail = new PHPMailer(true);
        // Server settings
        $mail->SMTPDebug = 0;
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();

        $mail->Host = 'mail.dctechmicro.com'; //'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mdmorta@dctechmicro.com';
        $mail->Password = 'incorrect';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 25;
        $mail->CharSet = 'utf-8';
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        //any suggestion about the sender??
        $mail->setFrom('mdmorta@dctechmicro.com', $senderName . " " . $sender);
        if ($sendTo != null)
            foreach ($sendTo as $item) {
                $item = (object) $item;
                $mail->addAddress($item->email, $item->name);
            }
        if ($CCTO != null)
            foreach ($CCTO as $item) {
                $item = (object) $item;
                $mail->addCC($item->email, $item->name);
            }

        //$mail->addReplyTo($request->email, 'Mailer');
        //$mail->addCC('pbismonte@dctechmicro.com');
        //$mail->addBCC('his-her-email@gmail.com');

        //Attachments (optional)
        // $mail->addAttachment('/var/tmp/file.tar.gz');			// Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');	// Optional name

        $mail->isHTML(true);                                                                     // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
    }
    public static function instance()
    {
        return new Logger();
    }
}
