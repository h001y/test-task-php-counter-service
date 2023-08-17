<?php

namespace App\Message;

class UpdateStatisticMessage
{
    private string $countryCode;

    public function __construct(string $countryCode)
    {
        $this->countryCode = $countryCode;
    }

    public function getCountryCode(): string
    {
        return strtoupper($this->countryCode);
    }
}