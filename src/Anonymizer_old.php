<?php

class  Anonymizer {

    public static function anonym(string $text, string $placeholder = "{_XXX_}", array $settings = ["NUMBERS", "TEL", "DATES", "EMAIL", "NAME", "SURNAME", "ADDRESS"]){
        $start = microtime(true);
        // $patterns = [];
        //Vícero mezer nahraď pouze jednou
        $text = preg_replace('/[ \t\p{Zs}]+/u', ' ', $text);

        if(in_array("TEL", $settings)){
            // $patterns['[%TEL%]'] = '/(\+420|420)?\s*(\d{3})\s*(\d{3})\s*(\d{3})/';
               $pattern = '/(?:\+\s*420|\s*420)?\s*\d{3}\s*\d{3}\s*\d{3}/';
               $text = preg_replace($pattern, $placeholder, $text);
        }

        if(in_array("EMAIL", $settings)){
            // $patterns['[%EMAIL%]'] = '/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}/';
            $pattern = '/[\p{L}_\-\.\+%0-9]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}/u';
            $text = preg_replace($pattern, $placeholder, $text);
        }

        if (in_array("ADDRESS", $settings)) {
            $cities = self::loadCsvToArray('obce.csv');
            foreach ($cities as $city) {
                $first_letter = mb_substr($city, 0, 1, 'UTF-8');
                $rest = mb_substr($city, 1, null, 'UTF-8');
                $c = $first_letter . '(?i:' . preg_quote($rest, '/') . ')';
                // $c = preg_quote($city, '/');
                $text = preg_replace('/(?!^)(?<![.!?] )(?<![.!?])(?:např.\s*)?' . $c . '([ ]*\d+)?(?!\p{L})/u', $placeholder, $text);
            }

            $streets = self::loadCsvToArray('ulice.csv');

            // Rozdělení seznamu ulic
            $chunks = array_chunk($streets, ceil(count($streets) / 70));

            $regex = '/(?!^)(?<![.!?] )(?<![.!?])(';
            $regex_end = ')([ ]*\d+(?:\s*\/\s*\d+)?)?(?!\p{L})/u';

            foreach ($chunks as $chunk){
                $pattern = $regex;
                foreach($chunk as $street){
                    $first_letter = mb_substr($street, 0, 1, 'UTF-8');
                    $rest = mb_substr($street, 1, null, 'UTF-8');
                    $s = $first_letter . '(?i:' . preg_quote($rest, '/') . ')';
                    $pattern .= $s . '|';
                }
                // Odstraníme poslední | z patternu
                $pattern = rtrim($pattern, '|');
                $pattern .= $regex_end;

                $text = preg_replace($pattern, $placeholder, $text);
            }


            // foreach ($streets as $street) {
            //     // $text = preg_replace('/(?!^)' . preg_quote($street, '/') . '(?!\p{L})/u', $placeholder, $text);
            //     $text = preg_replace('/(?!^)' . preg_quote($street, '/') . '\s*\d+(?:\/\d+)?(?!\p{L})/u', $placeholder, $text);
            // }

            $patterns = [];
            $patterns['[%PSC%]'] = '/\b\d{3} \d{2}\b/';
            $patterns['[%INCITY%]'] = '/(?<=\s[vV]\s)(\p{Lu}\p{Ll}{3,})/u'; // v Břeclavi, V Hodoníně, ...
            foreach ($patterns as $rep => $pattern) {
                $text = preg_replace($pattern, $placeholder, $text);
            }

            $text = preg_replace_callback('/(?:(městě|město|obci|obec|vesnici|vesnice)\s+?)(\p{Lu}\p{Ll}{3,})/u', function($matches) use ($placeholder) {
                return ' ' . trim($matches[1]) . ' ' . $placeholder;
            }, $text);

        }

        if (in_array("SURNAME", $settings)) {
            $surnames = self::loadCsvToArray('prijmeni.csv');
            
            // Rozdělení seznamu jmen na 900 částí
            $chunks = array_chunk($surnames, ceil(count($surnames) / 900));

            $p = preg_quote($placeholder, '/');

            $regex = '/(?<!\p{L})(\p{L}\.\s+|\p{Lu}+\p{Ll}*\s+|(?:[dD]en|[pP]anem|[pP]an[aíue]?|[pP]án|[sS]lečna|[sS]lečnou|[dD]ěkujeme|[dD]ěkuj[ui]|[dD]íky|pozdravem|úctou|[tT]o:)\,?\s*|(?:'.$p.'\s*))';
            $regex_end = '(?=[ ]*\p{Lu}\p{Ll}|[ ]*\p{L}\.|[ ]*'.$p.'|[.\s\n]*\z)(?!\p{L})/u';

            foreach ($chunks as $chunk){
                $pattern = $regex;
                $surnames_pattern = '';
                foreach($chunk as $i => $surname){

                    $root = $surname;
                    $variants = '[ayeu]|ovi|em';
                    $shortened = false;

                    // Pravidla pro odvozování základního tvaru a skloňování
                    if (preg_match('/(ová)$/u', $surname) && strlen($surname) > 4) {
                        $root = mb_substr($surname, 0, -1); // Odebereme -á
                        $variants = '[áaée]|ou';
                        $shortened = true;
                    } elseif (preg_match('/(ý|á)$/u', $surname) && strlen($surname) > 2) {
                        $root = mb_substr($surname, 0, -1); // Odebereme -ý nebo -á
                        $variants = '[áaýyée]|[éý]m|ého|ému|ou';
                        $shortened = true;
                    } elseif (preg_match('/(ec)$/u', $surname) && strlen($surname) > 3) {
                        $root = mb_substr($surname, 0, -2); // Odebereme -ec
                        $variants = 'c[ei]|ec|covi|cem';
                        $shortened = true;
                    } elseif (preg_match('/(ek|ík)$/u', $surname) && strlen($surname) > 3) {
                        $root = mb_substr($surname, 0, -2); // Odebrání -ek, -ík
                        $variants = '[íe]k|ka|kovi|kem';
                        $shortened = true;
                    } elseif (preg_match('/(a|o)$/u', $surname) && strlen($surname) > 2) {
                        $root = mb_substr($surname, 0, -1); // Odebereme poslední samohlásku
                        $variants = '[aoyu]|ou|ovi';
                        $shortened = true;
                    }

                    $first_letter = mb_substr($root, 0, 1, 'UTF-8');
                    $rest = mb_substr($root, 1, null, 'UTF-8');
                    
                    if($shortened)
                        $variants = "(?:".$variants.")";
                    else
                        $variants = "(?:".$variants.")?";
            
                    $surname_pattern = $first_letter . '(?i:' . preg_quote($rest, '/') . $variants . ')';
                    $surnames_pattern .= $surname_pattern . '|';
                }

                // Odstraníme poslední | z patternu
                $surnames_pattern = rtrim($surnames_pattern, '|');
                $pattern .= "($surnames_pattern)(?!\p{L})|(?<!\p{L})($surnames_pattern)";
                $pattern .= $regex_end;

                // Nahradíme všechny výskyty jmen v textu
                $text = preg_replace_callback($pattern, function($matches) use ($placeholder, $pattern) {
                    // dump($matches);
                    return trim($matches[1]) . ' ' . $placeholder;
                }, $text);
            }
        }

        if (in_array("NAME", $settings)) {
            $names = self::loadCsvToArray('jmena.csv');
            foreach ($names as $name) {
                $first_letter = mb_substr($name, 0, 1, 'UTF-8');
                $rest = mb_substr($name, 1, null, 'UTF-8');
                $n = $first_letter . '(?i:' . preg_quote($rest, '/') . ')';
                // $n = preg_quote($name, '/');
                $p = preg_quote($placeholder, '/');
                $text = preg_replace('/(?<!\p{L})(?<![.!?] )(?<![.!?])(?<!^)(?<!obchod\s)(?<!obchodu\s)(?<!obchodě\s)(?<!shop\s)(?<!shopu\s)(?<!shopě\s)'.$n.'(?!\p{L})|'.$n.'(?=\s*'.$p.')/u', $placeholder, $text);
                // $text = preg_replace('/(?<![\p{L}])(?<!^)' . preg_quote($name, '/') . '(?!\p{L})/u', $placeholder, $text);
            }
        }

        // if(in_array("RC", $settings)){
        //     $patterns['[%RC%]'] = '/\d{6}\/\d{4}|\d{6}\\\d{4}|\d{10}/';
        // }

        // if(in_array("BANK_ACCOUNT", $settings)){
        //     $patterns['[%BANK_ACCOUNT%]'] = '/\d{20}/';
        // }

        // if(in_array("IP", $settings)){
        //     $patterns['[%IP%]'] = '/(?:\d{1,3}\.){3}\d{1,3}/';
        // }

        // if(in_array("DATES", $settings)){
        //     $patterns['[%DATE%]'] = '/\b\d{1,2}\. ?\d{1,2}\. ?\d{4}\b/';
        // }

        if (in_array("NUMBERS", $settings)) { // všechny čísla krom částek a dat
            $pattern = '/\b(?!\d{1,2}\.\s?\d{1,2}\.\s?\d{4}\b)(?!\d{4}\b)(?!\d{1,3}(?:\s?\d{3})*(?:,\d+)?\s?([Kk]č|,-|.-|korun|EUR|USD|GBP|CZK|€|\$))\p{L}*\d{4,}\p{L}*\b(?!\s?([Kk]č|,-|.-|korun|EUR|€|\$|USD|GBP|CZK|\.\d{1,2}))/';
            $text = preg_replace($pattern, $placeholder, $text);
            
        }

        $end = microtime(true); // Uloží čas konce
        $execution_time = $end - $start; // Výpočet doby běhu
        // echo('Trvání: ' . round($execution_time, 5) . 's');

        return $text;
    }

    private static function loadCsvToArray($filename) {
        $filePath = __DIR__ . '/../../data/anonym_lists/' . $filename; // Cesta k souboru je o 2 úrovně výš
        if (!file_exists($filePath)) {
            throw new Exception("Soubor $filename nebyl nalezen. ($filePath)");
        }
    
        // Načtení souboru do pole
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
        // Převod z Windows-1250 do UTF-8
        foreach ($lines as &$line) {
            $line = mb_convert_encoding($line, "UTF-8");
        }
    
        return $lines;
    }
}

