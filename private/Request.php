<?php
namespace Private;

class Request {
    static function ip() : string {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
        elseif (!empty($_SERVER['REMOTE_ADDR'])) return $_SERVER['REMOTE_ADDR'];
        else return '';
    }

    static function userAgent() {
        return $_SERVER['HTTP_USER_AGENT']??'';
    }
}