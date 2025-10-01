<?php
declare(strict_types=1);

namespace NexiCheckout\Http;

use NexiCheckout\Http\Header\HeaderOption;

final class RequestHeaderOptions
{
    /**
     * @var array<string, HeaderOption>
     */
    private array $options = [];

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function with(HeaderOption $option): self
    {
        $this->options[$option::class] = $option;
        return $this;
    }

    /**
     * @return array<string,string>
     */
    public function toHeaders(): array
    {
        $headers = [];
        foreach ($this->options as $option) {
            $headers[$option->headerName()] = $option->headerValue();
        }

        return $headers;
    }
}
