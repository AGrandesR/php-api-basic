<?php

namespace Private\Utils;

use Error;

class HashTool{
    static function check(string $string, string $hash, $flag='') : string{
        $string=crypt($string, self::getSalt($flag));
        return hash_equals($string,$hash);
    }

    static function create(string $string, $flag='') : string{
        return crypt($string, self::getSalt($flag));
    }

    static function getSalt(string $flag='') : string{
        $err=[];
        $secretFlag = empty($flag) ? 'HASH_' : strtolower($flag) . '_HASH_';
        if(!isset($_ENV[$secretFlag . 'COST'])) $err[]=$secretFlag . 'COST is not defined in env file';
        if(!isset($_ENV[$secretFlag . 'ROUNDS'])) $err[]=$secretFlag . 'COST is not defined in env file';
        if(!isset($_ENV[$secretFlag . 'SECRET'])) $err[]=$secretFlag . 'COST is not defined in env file';
        if(!empty($err)) throw new Error(json_encode($err));
        $cost   = $_ENV[$secretFlag . 'COST'];      // HASH_COST=5
        $rounds = $_ENV[$secretFlag . 'ROUNDS'];      // HASH_ROUNDS=5000
        $secret = $_ENV[$secretFlag . 'SECRET'];    // HASH_SECRET=Silly
        return'$'.$cost.'$'.$rounds.'$'.$secret.'$';
    }
}