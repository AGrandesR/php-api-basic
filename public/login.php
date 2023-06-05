<?php
require '../vendor/autoload.php';

Private\Utils\Dotenv::load('../.env');

use Private\Auth;
use Private\Response;
use Private\Utils\Cryptor;
use Private\Utils\DatabaseTool;
use Private\Utils\HashTool As HT;
use Private\Utils\MailTool;

//CHECK POST BODY
if(!preg_match('/^\S+@\S+\.\S+$/',$_POST['mail'])) throw new Exception("Incorrect mail type", 1);
if(!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/',$_POST['pass'])) throw new Exception("Incorrect pass type", 1);

//SQL query
$sqlFindUser="SELECT * FROM users WHERE mail = :mail AND `password` = :pass";
$userData = DatabaseTool::sql('',$sqlFindUser,['mail'=>$_POST['mail'],'pass'=>HT::create($_POST['pass'])]);

if(is_array($userData)) Response::json([
    "status"=>"ok",
    "token"=>Auth::token($userData[0]['id'],$userData[0]['mail'],$userData[0]['role']??'user')
]);
else Response::json([
    "status"=>"ko"
]);