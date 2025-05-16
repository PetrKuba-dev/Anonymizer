<?php

namespace App\Loaders;

use Exception;

class CsvLoader
{
    /**
     * Načte CSV soubor a vrátí ho jako pole řetězců.
     *
     * @param string $filename Název CSV souboru (např. 'jmena.csv')
     * @param string $relativePath Relativní cesta od kořenové složky (např. 'data/anonym_lists/')
     * @return array Pole hodnot z CSV
     * @throws Exception Pokud soubor neexistuje nebo je prázdný
     */
    public static function load(string $filename, string $relativePath = 'data/cs/'): array
    {
        $filePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $relativePath . $filename;

        if (!file_exists($filePath)) {
            throw new Exception("Soubor $filename nebyl nalezen. ($filePath)");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!$lines) {
            throw new Exception("Soubor $filename je prázdný nebo se nepodařilo načíst.");
        }

        // Zkontroluj a převeď z Windows-1250 do UTF-8, pokud potřeba
        foreach ($lines as &$line) {
            $line = mb_convert_encoding($line, 'UTF-8');
        }

        return $lines;
    }
}
