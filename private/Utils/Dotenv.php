<?php
namespace Private\Utils;

use Exception;

class Dotenv {
    static function load(string $path) {
        $filePath = $path;

        // Abrir el archivo en modo lectura
        $file = fopen($filePath, 'r');

        if ($file) {
            // Leer el archivo línea por línea
            while (($line = fgets($file)) !== false) {
                // Procesar la línea
                if ($line === '' || strpos($line, '#') === 0) {
                    continue;
                }
                $arrLine=explode('=',$line);
                if(Count($arrLine)<2) continue;
                $key=$arrLine[0];
                unset($arrLine[0]);
                $value=implode('=',$arrLine);
                $value=trim(str_replace(array("\n", "\t"), '', $value));
                $_ENV[$key] = $value;
                putenv($line);
            }

            // Cerrar el archivo
            fclose($file);
        } else {
            throw new Exception("Error Processing Env file", 1);
            
        }
    }
}
