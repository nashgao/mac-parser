<?php

declare(strict_types=1);

namespace Nashgao\MacParser;

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
    public const BROADCAST = 'ffffffffffff';

    protected Parser|null $mac;

    /**
     * @param bool $compliment set the prefix as 0 as string
     * @param bool $full if compliment the mac to 6 digit or 12 digit
     * @param int $digit there might be a chance that the insufficient digit in hex is numeric (like 00000)
     *                   or it can be a mac address in dec (like 187649984473770), so the digit indicates
     *                   that if the length of the mac given equals to the digit then consider it as dec otherwise
     *                   consider it as hex
     * @throws InvalidMacException
     * @throws \Exception
     */
    public function __construct(string|array|int $mac, bool $compliment = true, bool $full = false, int $digit = 15)
    {
        $this->mac = match (true) {
            \is_array($mac) => new MacAddressReverseParser($mac),
            \is_int($mac) => new MacAddressParser(dechex($mac)),
            \is_string($mac) => \call_user_func(
                static function () use ($mac, $full, $compliment, $digit) {
                    if (empty($mac)) {
                        return null;
                    }

                    if (\is_numeric($mac) && \strlen($mac) === $digit) {
                        $mac = \dechex($mac);
                    }

                    $mac = Parser::normalizeMac($mac);
                    $macLen = \strlen($mac);
                    if ($compliment) {
                        if ($macLen % 2 !== 0) {
                            $mac = '0' . $mac;
                            ++$macLen;
                        }

                        if ($macLen > 6) {
                            $complimentDigit = 12 - $macLen;
                        } else {
                            $complimentDigit = ($full)
                            ? 12 - $macLen
                            : 6 - $macLen;
                        }

                        if ($complimentDigit !== 0) {
                            for ($index = 0; $index < $complimentDigit; ++$index) {
                                $mac = '0' . $mac;
                            }
                        }
                    }
                    return new MacAddressParser($mac);
                }
            ),
            default => null
        };

        if (\is_null($this->mac)) {
            throw new InvalidMacException(
                \sprintf('invalid type for mac address, array or string needed, but %s: %s provided', \gettype($mac), $mac)
            );
        }
    }

    public function __call($name, $arguments)
    {
        return $this->mac->{$name}($arguments);
    }

    public function isBroadcast(): bool
    {
        return $this->mac->getNormalized() === self::BROADCAST;
    }

    public function isMulticast(): bool
    {
        return \str_ends_with($this->mac->getFirstOctetsBinary(), '1');
    }

    public function isUnicast(): bool
    {
        return ! $this->isMulticast();
    }

    public function isUaa(): bool
    {
        return $this->isUnicast() && \substr($this->mac->getFirstOctetsBinary(), -2, 1) === '0';
    }

    public function isLaa(): bool
    {
        return $this->isUnicast() && \substr($this->mac->getFirstOctetsBinary(), -2, 1) === '1';
    }
}
