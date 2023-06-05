<?php
namespace Private;

class Response {
    static function json(array $body,int $httpcode=200) {
        http_response_code($httpcode);
    
        // Set the JSON content type header
        header('Content-Type: application/json');
        
        // Convert the body to JSON format
        $jsonBody = json_encode($body);
        
        // Print the JSON response
        echo $jsonBody;
        exit;
    }
    static function redirect($url) {
        header("Location: $url");
        exit;
    }

    static function unauthorized(){
        http_response_code(401);
        echo 'Unauthorized';
        exit;
    }

    static function html(string $string){
        if(is_file($string)) readfile($string);
        else echo $string;
        exit;
    }
}