<?php

declare(strict_types=1);


namespace Nashgao\MacParser;


class MacAddressReverseParser extends Parser
{
    protected int $mac;

    protected string $normalized;

    protected array $octets;

    protected string $firstOctetsBinary;

    protected string $type;

    public function __construct(int $octets)
    {

    }
}