<?php


declare(strict_types=1);

namespace Nashgao\MacParser;

use Exception;

/**
 * @method bool hasOut()
 * @method bool hasCid()
 * @method string toBinary(string $hex)
 * @method string getMac()
 * @method string getNormalized()
 * @method array getOctets()
 * @method string getFirstOctetsBinary()
 * @method string getType()
 */
class MacAddress
{
    const BROADCAST = 'ffffffffffff';

    protected Parser $mac;

    /**
     * @param string|array $mac
     * @throws Exception
     */
    public function __construct($mac)
    {
        $this->mac = is_array($mac)
            ? new MacAddressReverseParser($mac)
            : (function () use ($mac) {
                if (is_numeric($mac)) {
                    $mac = dechex($mac);
                }
                return new MacAddress($mac);
            })();
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

    public function __call($name, $arguments)
    {
        return $this->mac->{$name}($arguments);
    }
}
