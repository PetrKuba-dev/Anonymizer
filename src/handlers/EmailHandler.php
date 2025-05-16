<?php

namespace App\Handlers;

use App\Handlers\AnonymizationHandlerInterface;

class EmailHandler implements AnonymizationHandlerInterface
{
    public function handle(string $text, string $placeholder): string
    {
        $pattern = '/[\p{L}_\-\.\+%0-9]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}/u';
        return preg_replace($pattern, $placeholder, $text);
    }
}
