<?php

namespace App;

use App\Handlers\TelHandler;
use App\Handlers\EmailHandler;
use App\Handlers\NameHandler;
use App\Handlers\SurnameHandler;
use App\Handlers\AddressHandler;
use App\Handlers\NumberHandler;
use App\Handlers\DateHandler;
use App\Handlers\AnonymizationHandlerInterface;

class Anonymizer {

    /**
     * Seznam všech dostupných handlerů.
     * @var array<string, AnonymizationHandlerInterface>
     */
    private array $availableHandlers;

    /**
     * Konstruktor – zaregistruje handlery
     */
    public function __construct() {
        $this->availableHandlers = [
            'TEL'      => new TelHandler(),
            'EMAIL'    => new EmailHandler(),
            'ADDRESS'  => new AddressHandler(),
            'SURNAME'  => new SurnameHandler(),
            'NAME'     => new NameHandler(),
            'NUMBERS'  => new NumberHandler(),
            'DATES'    => new DateHandler(), // můžeš i později doplnit
        ];
    }

    /**
     * Hlavní metoda pro anonymizaci textu
     */
    public function anonymize(string $text, array $settings = [], string $placeholder = '{_XXX_}'): string {
        // $startTime = microtime(true);

        foreach ($settings as $setting) {
            if (!array_key_exists($setting, $this->availableHandlers)) {
                $available = implode(', ', array_keys($this->availableHandlers));
                throw new \InvalidArgumentException("Unknown anonymization key: '$setting'. Allowed values are: $available");
            }
        }

        // Pokud není žádné nastavení, anonymizuj vše
        if (empty($settings)) {
            $settings = array_keys($this->availableHandlers);
        }

        // Normalizuj mezery
        $text = preg_replace('/[ \t\p{Zs}]+/u', ' ', $text);

        // Pro každý vybraný typ spusť handler
        foreach ($this->availableHandlers as $key => $handler) {
            if (in_array($key, $settings)) {
                $text = $handler->handle($text, $placeholder);
            }
        }

        // $duration = microtime(true) - $startTime;
        // echo "Doba běhu: " . round($duration, 5) . "s\n";

        return $text;
    }
}
