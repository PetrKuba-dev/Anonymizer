<?php

namespace App\Handlers;

use App\Handlers\AnonymizationHandlerInterface;
use App\Loaders\CsvLoader;

class NameHandler implements AnonymizationHandlerInterface
{
    public function handle(string $text, string $placeholder): string
    {
        $names = CsvLoader::load('names.csv');

        foreach ($names as $name) {
            $first_letter = mb_substr($name, 0, 1, 'UTF-8');
            $rest = mb_substr($name, 1, null, 'UTF-8');
            $n = $first_letter . '(?i:' . preg_quote($rest, '/') . ')';
            // $n = preg_quote($name, '/');
            $p = preg_quote($placeholder, '/');
            $text = preg_replace('/(?<!\p{L})(?<![.!?] )(?<![.!?])(?<!^)(?<!obchod\s)(?<!obchodu\s)(?<!obchodě\s)(?<!shop\s)(?<!shopu\s)(?<!shopě\s)'.$n.'(?!\p{L})|'.$n.'(?=\s*'.$p.')/u', $placeholder, $text);
            // $text = preg_replace('/(?<![\p{L}])(?<!^)' . preg_quote($name, '/') . '(?!\p{L})/u', $placeholder, $text);
        }

        return $text;
    }
}
