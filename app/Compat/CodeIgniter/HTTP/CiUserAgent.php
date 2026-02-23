<?php

namespace App\Compat\CodeIgniter\HTTP;

class CiUserAgent
{
    public function __construct(private readonly string $agent)
    {
    }

    public function getAgentString(): string
    {
        return $this->agent;
    }

    public function isMobile(): bool
    {
        return str_contains(strtolower($this->agent), 'mobile');
    }
}
