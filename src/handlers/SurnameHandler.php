<?php

namespace App\Handlers;

use App\Handlers\AnonymizationHandlerInterface;
use App\Loaders\CsvLoader;

class SurnameHandler implements AnonymizationHandlerInterface
{
    public function handle(string $text, string $placeholder): string
    {
        $surnames = CsvLoader::load('surnames.csv');
            
        // Rozdělení seznamu jmen na 900 částí
        $chunks = array_chunk($surnames, ceil(count($surnames) / 900));

        $p = preg_quote($placeholder, '/');

        $regex = '/(?<!\p{L})(\p{L}\.\s+|\p{Lu}+\p{Ll}*\s+|(?:[dD]en|[pP]anem|[pP]an[aíue]?|[pP]án|[sS]lečna|[sS]lečnou|[dD]ěkujeme|[dD]ěkuj[ui]|[dD]íky|pozdravem|úctou|[tT]o:|[jJ]méno:|[nN]ame:)\,?\s*|(?:'.$p.'\s*))';
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
                return trim($matches[1]) . ' ' . $placeholder;
            }, $text);
        }

        return $text;
    }
}
