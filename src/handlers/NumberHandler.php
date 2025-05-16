<?php

namespace App\Handlers;

use App\Handlers\AnonymizationHandlerInterface;

class NumberHandler implements AnonymizationHandlerInterface
{
    public function handle(string $text, string $placeholder): string
    {
        $pattern = '/\b(?!\d{1,2}\.\s?\d{1,2}\.\s?\d{4}\b)(?!\d{4}\b)(?!\d{1,3}(?:\s?\d{3})*(?:,\d+)?\s?([Kk]č|,-|.-|korun|EUR|USD|GBP|CZK|€|\$))\p{L}*\d{4,}\p{L}*\b(?!\s?([Kk]č|,-|.-|korun|EUR|€|\$|USD|GBP|CZK|\.\d{1,2}))/';
        return preg_replace($pattern, $placeholder, $text);
    }
}
