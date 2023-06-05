<?php

namespace Private\Utils;

use Error;
use Exception;
use PDOException;
use PDO;

/*
EXAMPLE ENV:
DB_FLAG_TYPE=           # mysql psql
DB_FLAG_HOST=
DB_FLAG_USER=
DB_FLAG_PASS=
DB_FLAG_DTBS=
DB_FLAG_PORT=
DB_FLAG_CHAR=           # DEFAULT UTF-8

Example:

$DB = new DatabaseTool();
*/

class DatabaseTool {
    protected PDO $pdo;
    protected bool $operative;
    public string $lastError;

    function __construct(string $flag='') {
        try {
            $flag = (empty($flag))? 'DB_': 'DB_'.$flag.'_';
            $type=$_ENV[$flag . 'TYPE'];
            $host=$_ENV[$flag . 'HOST'];
            $user=$_ENV[$flag . 'USER'];
            $pass=$_ENV[$flag . 'PASS'];
            $dtbs=$_ENV[$flag . 'DTBS'];
            $port=$_ENV[$flag . 'PORT'];
            $char=isset($_ENV[$flag . 'CHAR']) ? $_ENV[$flag . 'CHAR'] : 'UTF8';
            $dsn = "$type:host=$host;port=$port;dbname=$dtbs;charset=$char";

            $this->pdo = new PDO($dsn, $user, $pass);
        
            $this->operative=true;
        } catch (Error | Exception | PDOException $e) {
            $this->operative=false;
            $this->lastError = "Construct:" . $e->getMessage() . " $dsn";
            echo $this->lastError;die;
        }
    }

    public function isOperative() {
        return $this->operative;
    }

    public function query(string $sql, array $values=[]) : array | bool {
        try {
            if(empty($values)) {
                $statement = $this->pdo->query($sql);
            } else {
                $statement = $this->pdo->prepare($sql);
                foreach ($values as $key=>&$value){
                    //echo "$key - $value " . $this->getPdoType($value) ."\n";
                    $statement->bindParam("$key", $value, $this->getPdoType($value));
                }
                $statement->execute();
            }
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return empty($result) ? true : $result;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            echo $this->lastError;die;
            return false;
        }

    }

    private function getPdoType($value){
        switch(gettype($value)) {
            case ("boolean"):
                return PDO::PARAM_BOOL;
            case ("integer"):
                return PDO::PARAM_INT;
            case ("NULL"):
                return PDO::PARAM_NULL;
            default:
                return PDO::PARAM_STR;
        }
    }
    static function sql(string $flag='', string $sql='SHOW DATABASES;', array $values=[]) : array | bool {
        return (new self($flag))->query($sql, $values);
    }
}
?>