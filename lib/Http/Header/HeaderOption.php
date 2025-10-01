<?php
declare(strict_types=1);

namespace NexiCheckout\Http\Header;

interface HeaderOption
{
    public function headerName(): string;

    public function headerValue(): string;
}
