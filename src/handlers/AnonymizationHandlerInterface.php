<?php

namespace App\Handlers;

interface AnonymizationHandlerInterface
{
    public function handle(string $text, string $placeholder): string;
}
