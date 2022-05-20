<?php

declare(strict_types=1);

namespace Nashgao\MacParser;

/**
 * parses the mac address from a octet.
 */
class MacAddressReverseParser extends Parser
{
    protected MacAddressParser $mac;

    /**
     * @throws \Exception
     */
    public function __construct(array $octets)
    {
        $replacement = [];
        foreach ($octets as $octet) {
            $replacement[] = $this->compliment((string) $octet);
        }

        $octets = $replacement;

        // compliment the digits
        $octets = \implode('', $octets);
        $octetsLen = \strlen($octets);
        $mac = '';
        for ($i = 0; $i < $octetsLen; $i += 8) {
            $mac .= \dechex(\bindec(\substr($octets, 0, 8)));
            $octets = \substr($octets, -(\strlen($octets) - 8));
        }

        $this->mac = new MacAddressParser($mac);
    }

    public function hasOui(): bool
    {
        return $this->mac->getType() === self::UNIQUE;
    }

    public function hasCid(): bool
    {
        return $this->mac->getType() === self::LOCAL;
    }

    public function toBinary(string $hex): string
    {
        return $this->mac->toBinary($hex);
    }

    public function getMac(): string
    {
        return $this->mac->getMac();
    }

    public function getNormalized(): string
    {
        return $this->mac->getNormalized();
    }

    public function getOctets(): array
    {
        return $this->mac->getOctets();
    }

    public function getFirstOctetsBinary(): string
    {
        return $this->mac->getFirstOctetsBinary();
    }

    public function getType(): string
    {
        return $this->mac->getType();
    }

    public function isValid(): bool
    {
        return $this->mac->isValid();
    }
}
