<?php


declare(strict_types=1);

namespace Nashgao\MacParser;

use Exception;
use Nashgao\MacParser\Exception\InvalidMacException;

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
     * @param bool $compliment set the prefix as 0 as string
     * @param int $digit there might be a chance that the insufficient digit in hex is numeric (like 00000)
     *                   or it can be a mac address in dec (like 187649984473770), so the digit indicates
     *                   that if the length of the mac given equals to the digit then consider it as dec otherwise
     *                   consider it as hex
     * @throws Exception
     */
    public function __construct($mac, bool $compliment = false, int $digit = 15)
    {
        $this->mac = is_array($mac)
            ? new MacAddressReverseParser($mac)
            : (function () use ($digit, $mac, $compliment) {
                if (! is_string($mac)) {
                    throw new InvalidMacException(
                        sprintf('invalid type for mac address, array or string needed, but %d provided', gettype($mac))
                    );
                }
                $macLen = strlen($mac);
                if (is_numeric($mac) and $macLen === $digit) {
                    $mac = dechex($mac);
                }

                if($compliment and $macLen % 2 !== 0) {
                    $mac = '0' . $mac;
                }

                return new MacAddressParser($mac);
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
