<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../vendor/autoload.php';

Private\Utils\Dotenv::load('../.env');

use Private\Utils\Cryptor;
use Private\Utils\DatabaseTool;
use Private\Utils\HashTool As HT;
use Private\Utils\MailTool;

try {
    if($_SERVER['REQUEST_METHOD']=='POST'){
            //CHECK POST BODY
            if(!preg_match('/^\S+@\S+\.\S+$/',$_POST['mail'])) throw new Exception("Incorrect mail type", 1);
            if(!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/',$_POST['pass'])) throw new Exception("Incorrect pass type", 1);
    
            //SQL query
            $sqlFindUser="SELECT * FROM users WHERE mail = :mail;";
            $userData = DatabaseTool::sql('',$sqlFindUser,['mail'=>$_POST['mail']]);
            //Check response data

            //region DELETE preuser data if exist with this mail
            $sqlDeletePreuserData="DELETE FROM preusers WHERE mail = :mail;";
            DatabaseTool::sql('',$sqlDeletePreuserData,['mail'=>$_POST['mail']]);
            //endregion

            if(is_array($userData)) {
                throw new Exception("The email is used.", 1);   
            }
    
            $data = [
                "mail"=>$_POST['mail'],
                "pass"=>HT::create($_POST['pass'])
            ];
    
            //$token = HT::create(json_encode($data));
            $data['token'] = tokenGenerator();
            //region SAVE preuser
            $db = new DatabaseTool('');
            DatabaseTool::sql('',"INSERT INTO preusers (token, mail, password) VALUES (:token, :mail, :pass)", $data);
            //endregion
    
            //SEND MAIL WITH TOKEN
            $mail = new MailTool();
            $mail->addAddress($data['mail']);
            $mail->setSubject("Confirm register");

            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
                $protocol = 'https';
            } else{
                $protocol = 'http';
            }
            $currentURL = $protocol . '://' . $_SERVER['SERVER_NAME'] . (isset($_SERVER['SERVER_PORT']) ? ':'. $_SERVER['SERVER_PORT'] : '') .$_SERVER['REQUEST_URI'];
            $baseURL = strtok($currentURL, '?');
            
            $registerURL="$baseURL/register.php?register=".$data['token'];

            $htmlBody=str_replace('{{URL}}',$registerURL,file_get_contents('../private/Templates/RegisterMail.html'));
            
            $altBody="Confirm:$registerURL";
            $mail->setBody($htmlBody,$altBody);
            $mail->send() ?? throw new Exception("Error sending the mail");
            Private\Response::json([
                "status"=>"ok"
            ],201);
    } else {
        //SQL query
        $sqlFindPreuser="SELECT * FROM preusers WHERE token = :token;";
        try{
            $preuserData = DatabaseTool::sql('',$sqlFindPreuser,['token'=>$_GET['register']]);
        } catch(Exception $e) {
            Private\Response::json([
                "error"=>1,
                "message"=>$e->getMessage(),
                "file"=>$e->getFile(),
                "line"=>$e->getLine(),
                "code"=>$e->getCode()
            ],400);
        }
        //Check response data
        if(!is_array($preuserData)) throw new Exception("We dont have preusers data");
        $preuserData=$preuserData[0];
        //SQL delete
        $sqlDeletePreuserData="DELETE FROM preusers WHERE token = :token;";
        DatabaseTool::sql('',$sqlDeletePreuserData,['token'=>$_GET['register']]);
        //IGNORE $preuserData to next SQL insert
        unset($preuserData['token']);
        //SQL insert
        $sqlSetUserData="INSERT INTO users (mail, password) VALUES (:mail, :password);";
        DatabaseTool::sql('',$sqlSetUserData, $preuserData);
        //Happy ending
        Private\Response::html('../private/Templates/RegisterLanding.html');
        Private\Response::json([
            "status"=>"ok"
        ],201);
    }
} catch(Exception $e){
    Private\Response::json([
        "error"=>1,
        "message"=>$e->getMessage(),
        "file"=>$e->getFile(),
        "line"=>$e->getLine(),
        "code"=>$e->getCode()
    ],400);
}


function tokenGenerator($size=255){
    // Generar un número entero aleatorio dentro del rango válido
    $randomNumber = random_int(0, $size);

    // Generar una cadena de caracteres aleatorios
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, $randomNumber);
}