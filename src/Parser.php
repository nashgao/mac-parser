<?php

declare(strict_types=1);

namespace Nashgao\MacParser;

abstract class Parser
{
    /**
     * matching patterns of mac addresses.
     */
    public const PLAIN = ('/^[0-9A-Fa-f]{12}$/');

    public const HYPHEN = ('/^([0-9A-Fa-f]{2}[-]{1}){5}[0-9A-Fa-f]{2}$/');

    public const COLON = ('/^([0-9A-Fa-f]{2}[:]{1}){5}[0-9A-Fa-f]{2}$/');

    public const DOT = ('/^([0-9A-Fa-f]{4}[.]{1}){2}[0-9A-Fa-f]{4}$/');

    public const NOT_DIGITS = ('/[^0-9A-Fa-f]/');

    public const TWO_DIGITS = ('/[0-9a-f]{2}/');

    public const FOUR_DIGITS = ('/[0-9a-f]{4}/');

    public const SIX_DIGITS = ('/[0-9a-f]{6}/');

    public const EIGHT_DIGITS = ('/[0-9a-f]{8}/');

    public const UNKNOWN = 'unknown';

    public const UNIQUE = 'unique';

    public const LOCAL = 'local';

    protected string $normalized;

    public static function normalizeMac(string $mac): string
    {
        return \strtolower(\preg_replace(['/:/', '/-/'], '', $mac));
    }

    abstract public function isValid(): bool;

    abstract public function hasOui(): bool;

    abstract public function hasCid(): bool;

    abstract public function toBinary(string $hex): string;

    abstract public function getMac(): string;

    abstract public function getNormalized(): string;

    abstract public function getOctets(): array;

    abstract public function getFirstOctetsBinary(): string;

    abstract public function getType(): string;

    protected function normalize(): self
    {
        $this->normalized = \strtolower(\preg_replace(['/:/', '/-/'], '', $this->mac));
        return $this;
    }

    protected function compliment(string $octets): string
    {
        $length = \strlen($octets);
        if ($length < 8) {
            for ($i = 0; $i < (8 - $length); ++$i) {
                $octets = '0' . $octets;
            }
        }
        return $octets;
    }
}
