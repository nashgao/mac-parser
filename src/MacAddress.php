<?php


declare(strict_types=1);

namespace Nashgao\MacParser;

use Exception;

class MacAddress
{
    const BROADCAST = 'ffffffffffff';

    protected MacAddressParser $mac;

    /**
     * @param string|array $mac
     * @throws Exception
     */
    public function __construct($mac)
    {
        $this->mac = is_string($mac) ? new MacAddressParser($mac) : new MacAddressReverseParser($mac);
    }

    public function isBroadcast(): bool
    {
        return $this->mac->getNormalized() === self::BROADCAST;
    }

    public function isMulticast(): bool
    {
        return substr($this->mac->getFirstOctetsBinary(), -1) === '1';
    }

    public function isUnicast(): bool
    {
        return ! $this->isMulticast();
    }

    public function isUaa(): bool
    {
        return $this->isUnicast() and substr($this->mac->getFirstOctetsBinary(), -2, 1) === '0';
    }

    public function isLaa(): bool
    {
        return $this->isUnicast() and substr($this->mac->getFirstOctetsBinary(), -2, 1) === '1';
    }
}
