<?php

declare(strict_types=1);


namespace Nashgao\MacParser;

class Parser
{
    /**
     * matching patterns of mac addresses.
     */
    const PLAIN = ('/^[0-9A-Fa-f]{12}$/');

    const HYPHEN = ('/^([0-9A-Fa-f]{2}[-]{1}){5}[0-9A-Fa-f]{2}$/');

    const COLON = ('/^([0-9A-Fa-f]{2}[:]{1}){5}[0-9A-Fa-f]{2}$/');

    const DOT = ('/^([0-9A-Fa-f]{4}[.]{1}){2}[0-9A-Fa-f]{4}$/');

    const NOT_DIGITS = ('/[^0-9A-Fa-f]/');

    const TWO_DIGITS = ('/[0-9a-f]{2}/');

    const FOUR_DIGITS = ('/[0-9a-f]{4}/');

    const SIX_DIGITS = ('/[0-9a-f]{6}/');

    const EIGHT_DIGITS = ('/[0-9a-f]{8}/');

    const UNKNOWN = 'unknown';

    const UNIQUE = 'unique';

    const LOCAL = 'local';


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

    protected function compliment(string $octets): string
    {
        $length = strlen($octets);
        if ($length < 8) {
            for ($i = 0; $i < (8 - $length); $i++) {
                $octets = '0' . $octets;
            }
        }
        return $octets;
    }

    abstract public function hasOui(): bool;
    abstract public function hasCid(): bool;
    abstract public function toBinary(string $hex): string;
    abstract public function getMac(): string;
    abstract public function getNormalized(): string;
    abstract public function getOctets(): array;
    abstract public function getFirstOctetsBinary(): string;
    abstract public function getType(): string;
}
