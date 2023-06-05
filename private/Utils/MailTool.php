<?php

namespace Private\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailTool {
    protected string $subject;
    protected string $body;

    private PHPMailer $mail;

    function __construct(string $flag='') {
        $this->mail = new PHPMailer(true);

        $flag=(empty($flag))?'MAIL_':$flag.'_MAIL_';

        $Host=$_ENV[$flag . 'HOST'];
        $Port=$_ENV[$flag . 'PORT'];
        $Username=$_ENV[$flag . 'USERNAME'];
        $Password=$_ENV[$flag . 'PASSWORD'];
        $Security=$_ENV[$flag . 'SECURITY'] ?? false;
        $humanName=$_ENV[$flag . 'SENDNAME'] ?? 'TEST';
        
        //CONFIG
        // $this->mail->SMTPDebug      = SMTP::Debug;
        // $this->mail->SMTPDebug      = false;
        $this->mail->isSMTP();
        $this->mail->Host           = $Host;
        $this->mail->SMTPAuth       = true;
        $this->mail->Username       = $Username;
        $this->mail->Password       = $Password;
        $this->mail->SMTPSecure     = 'tls';
        // $this->mail->SMTPAutoTLS    = false;
        $this->mail->Port           = $Port;
        $this->mail->CharSet = "UTF-8";
        //MAIL OPTIONS
        $this->mail->setFrom($this->mail->Username, $humanName);
    }

    function addAddress(string $mail, string $name='') : void {
        if($name=='')$name=$mail;
        $this->mail->addAddress($mail, $name);
    }
    function setSubject(string $subject) {
        $this->mail->Subject = $subject;
    }
    function setBody(string $body, string $altBody='') {
        $this->mail->isHTML(true); 
        $this->mail->Body = $body;
        $this->mail->AltBody = ($altBody=='')? $body : $altBody;
        $this->mail->ContentType = "text/html; charset=UTF-8";
    }

    function send(): bool {
        return $this->mail->send() ? true : false;
    }
}

/*
// $mail->addAddress('ellen@example.com');               //Name is optional
// $mail->addReplyTo('info@example.com', 'Information');
// $mail->addCC('cc@example.com');
// $mail->addBCC('bcc@example.com');
*/