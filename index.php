<?php

require_once 'vendor/autoload.php';

use App\Anonymizer;

$text = "Pan Novák žije na ulici Nová 34, Brno";

$anonymizer = new Anonymizer();

$result = $anonymizer->anonymize($text, ['SURNAME', 'ADDRESS'], '{_XXX_}');

echo $result;
