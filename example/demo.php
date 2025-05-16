<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Anonymizer;

$anonymizer = new Anonymizer();

$text = <<<TEXT
Pan Jan Novák bydlí na adrese Dlouhá 45, Praha. Jeho email je jan.novak@example.com a telefon je +420 777 123 456.
TEXT;

$settings = ['NAME', 'SURNAME', 'ADDRESS', 'EMAIL', 'TEL', 'NUMBERS', 'DATES'];

try {
    $anonymizedText = $anonymizer->anonymize($text, $settings);

    echo "===============================\n";
    echo "🎯 PŮVODNÍ TEXT:\n";
    echo "===============================\n";
    echo $text . "\n\n";

    echo "===============================\n";
    echo "🛡️  ANONYMIZOVANÝ TEXT:\n";
    echo "===============================\n";
    echo $anonymizedText . "\n";
} catch (\InvalidArgumentException $e) {
    echo "❌ Chyba: " . $e->getMessage() . "\n";
}
