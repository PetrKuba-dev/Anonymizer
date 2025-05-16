<?php

namespace App\Handlers;

use App\Handlers\AnonymizationHandlerInterface;
use App\Loaders\CsvLoader;

class AddressHandler implements AnonymizationHandlerInterface
{
    public function handle(string $text, string $placeholder): string
    {

        $cities = CsvLoader::load('cities.csv');
        foreach ($cities as $city) {
            $first_letter = mb_substr($city, 0, 1, 'UTF-8');
            $rest = mb_substr($city, 1, null, 'UTF-8');
            $c = $first_letter . '(?i:' . preg_quote($rest, '/') . ')';
            $text = preg_replace('/(?!^)(?<![.!?] )(?<![.!?])(?:např.\s*)?' . $c . '([ ]*\d+)?(?!\p{L})/u', $placeholder, $text);
        }

        $streets = CsvLoader::load('streets.csv');

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

        $patterns = [];
        $patterns['[%PSC%]'] = '/\b\d{3} \d{2}\b/';
        $patterns['[%INCITY%]'] = '/(?<=\s[vV]\s)(\p{Lu}\p{Ll}{3,})/u'; // v Břeclavi, V Hodoníně, ...
        foreach ($patterns as $rep => $pattern) {
            $text = preg_replace($pattern, $placeholder, $text);
        }

        $text = preg_replace_callback('/(?:(městě|město|obci|obec|vesnici|vesnice)\s+?)(\p{Lu}\p{Ll}{3,})/u', function($matches) use ($placeholder) {
            return ' ' . trim($matches[1]) . ' ' . $placeholder;
        }, $text);
        
        return $text;
    }
}
