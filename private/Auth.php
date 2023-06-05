<?php
namespace Private;

use Exception;
use Private\Utils\Cryptor;

class Auth {
    static function user($roles){
        try {
            // Retrieve the "token" header
            $token = isset($_SERVER['HTTP_TOKEN']) ? $_SERVER['HTTP_TOKEN'] : '';
    
            if(empty($token)) throw new Exception("Auth error: No token in header", 401);
            
            $data = Cryptor::decrypt($token, $_ENV['CRYPTOR_SECRET']);
    
            if(!in_array($data['role'],$roles) && $data['role']!=='root') throw new Exception("Auth error: Unauthorized role", 401);
    
            $expirationTimeInSeconds=3600;
            if((time()-$data['time'])>$expirationTimeInSeconds) throw new Exception("Auth error: Expiration", 401);
    
            if($data['ip']!==Request::ip()) throw new Exception("Auth error: IP not match with original", 401);
    
            if($data['ua']!==Request::userAgent()) throw new Exception("Auth error: User Agent not match with original", 401);
            return array(true,'');
        } catch(Exception $e) {
            if($e->getCode()==401) return array(false,$e->getMessage());
            throw $e;
        }
    }

    static function token($id, $mail, $role='user') : string {
        return Cryptor::encrypt([
            'id'=>$id,
            'mail'=>$mail,
            'role'=>$role,
            'time'=>time(),
            'ip'=>Request::ip(),
            'ua'=>Request::userAgent()
        ],$_ENV['CRYPTOR_SECRET']);
    }
}





