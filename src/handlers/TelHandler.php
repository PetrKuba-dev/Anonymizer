<?php

namespace App\Handlers;

use App\Handlers\AnonymizationHandlerInterface;

class TelHandler implements AnonymizationHandlerInterface
{
    public function handle(string $text, string $placeholder): string
    {
        $pattern = '/(?:\+\s*420|\s*420)?\s*\d{3}\s*\d{3}\s*\d{3}/';
        return preg_replace($pattern, $placeholder, $text);
    }
}
