<?php

namespace App\Handlers;

use App\Handlers\AnonymizationHandlerInterface;

class DateHandler implements AnonymizationHandlerInterface
{
    public function handle(string $text, string $placeholder): string
    {
        $pattern = '/\b\d{1,2}\.\s?\d{1,2}\.\s?\d{4}\b/';
        return preg_replace($pattern, $placeholder, $text);
    }
}
