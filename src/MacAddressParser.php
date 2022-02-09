<?php


declare(strict_types=1);

namespace Nashgao\MacParser;

use Nashgao\MacParser\Exception\InvalidMacException;

class MacAddressParser extends Parser
{
    protected string $mac;

    protected array $octets;

    protected string $firstOctetsBinary;

    protected string $type;

    /**
     * @param $mac
     * @throws \Exception
     */
    public function __construct(string $mac)
    {
        $this->mac = $mac;
        $this->normalize();
        if (! $this->isValid()) {
            throw new InvalidMacException(
                sprintf('invalid mac address, %s provided', $mac)
            );
        }

        $this->extractOctets()
            ->firstOctets()
            ->extractType();
    }

    public function hasOui(): bool
    {
        return $this->type === self::UNIQUE;
    }

    public function hasCid(): bool
    {
        return $this->type === self::LOCAL;
    }

    public function toBinary(string $hex): string
    {
        return base_convert($hex, 16, 2);
    }

    public function getMac(): string
    {
        return $this->mac;
    }

    public function getNormalized(): string
    {
        return $this->normalized;
    }

    public function getOctets(): array
    {
        return $this->octets;
    }

    public function getFirstOctetsBinary(): string
    {
        return $this->firstOctetsBinary;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isValid(): bool
    {
        switch (true) {
            case preg_match(self::SIX_DIGITS, $this->normalized):
            case preg_match(self::EIGHT_DIGITS, $this->normalized):
            case preg_match(self::COLON, $this->normalized):
            case preg_match(self::HYPHEN, $this->normalized):
            case preg_match(self::PLAIN, $this->normalized):
                return true;
            default:
                return false;
        }
    }

    protected function extractOctets(): MacAddressParser
    {
        if (! $this->isNormalized()) {
            $this->normalize();
        }

        preg_match_all(self::TWO_DIGITS, $this->normalized, $matches, PREG_PATTERN_ORDER);
        $this->octets = $matches[0];
        foreach ($this->octets as &$octet) {
            $octet = $this->toBinary($octet);
        }
        return $this;
    }

    protected function firstOctets(): MacAddressParser
    {
        $this->firstOctetsBinary = $this->octets[0];
        return $this;
    }

    /**
     * The two least-significant bits in the first octet of
     * an extended identifier determine whether it is an EUI.
     *
     * The four least-significant bits in the first octet of
     * an extended identifier determine whether it is an ELI.
     */
    protected function extractType(): MacAddressParser
    {
        if (substr($this->firstOctetsBinary, -2) == '00') {
            $this->type = self::UNIQUE;
        } elseif (substr($this->firstOctetsBinary, -4) == '1010') {
            $this->type = self::LOCAL;
        } else {
            $this->type = self::UNKNOWN;
        }
        return $this;
    }

    /**
     * @return false|int
     */
    private function isNormalized()
    {
        return preg_match(self::PLAIN, $this->mac);
    }
}
