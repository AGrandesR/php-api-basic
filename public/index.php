<?php

use Private\Response;

require '../vendor/autoload.php';

Private\Utils\Dotenv::load('../.env');

$auth=Private\Auth::user(['user','admin']);

list($auth,$err)=Response::unauthorized();
if(!$auth) Response::json([
    'status'=>'ko',
    'error'=>$err
],401);

//if(!$auth) return Private\Response::redirect('/login.php');