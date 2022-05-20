<?php

declare(strict_types=1);

namespace Nashgao\Testing\Cases;

use Nashgao\MacParser\Exception\InvalidMacException;
use Nashgao\MacParser\MacAddress;
use Nashgao\Testing\AbstractTest;

/**
 * @internal
 * @coversNothing
 */
class TestParser extends AbstractTest
{
    /**
     * @group mac
     */
    public function testMac()
    {
        $mac = 'aa:aa:aa:aa:aa:aa';
        $parser = new MacAddress($mac);
        $this->assertEquals('aaaaaaaaaaaa', $parser->getNormalized());

        $mac = 'aaaaaaaaaaaa';
        $parser = new MacAddress($mac);
        $this->assertEquals('aaaaaaaaaaaa', $parser->getNormalized());
    }

    /**
     * @group invalid-mac
     */
    public function testInvalidMac()
    {
        $invalidMac = 'aaa';
        try {
            $parser = new MacAddress($invalidMac, false);
        } catch (InvalidMacException $e) {
            $this->assertInstanceOf(InvalidMacException::class, $e);
        }
    }

    /**
     * @group reverse-mac
     */
    public function testReverseMac()
    {
        // test parse mac address from octets
        $octets = [
            10101010,
            10101010,
            10101010,
            10101010,
            10101010,
            10101010,
        ];
        $parser = new MacAddress($octets);
        $this->assertEquals('aaaaaaaaaaaa', $parser->getNormalized());

        // test compliment
        $octets = [
            1101010,
            10101010,
            10101010,
            10101010,
            10101010,
            10101010,
        ];
        $parser = new MacAddress($octets);
        $this->assertEquals('6aaaaaaaaaaa', $parser->getNormalized());
        $this->assertTrue(true);
    }

    /**
     * @group insufficient-mac
     */
    public function testMacWithInsufficientDigits()
    {
        $octets = [
            10101010,
            10101010,
            10101010,
        ];
        $parser = new MacAddress($octets);
        $this->assertEquals('aaaaaa', $parser->getNormalized());
    }

    /**
     * @group insufficient-string-mac
     */
    public function testMacWithInsufficientString()
    {
        // 5 digit
        $mac = 'aaaaa';
        $parser = new MacAddress($mac);
        $this->assertEquals('0aaaaa', $parser->getNormalized());

        $mac = 'aaaaaaa';
        $parser = new MacAddress($mac);
        $this->assertEquals('00000aaaaaaa', $parser->getNormalized());
    }

    /**
     * @group numeric-mac
     */
    public function testNumericMac()
    {
        $mac = 187649984473770;
        $parser = new MacAddress($mac);
        $this->assertEquals('aaaaaaaaaaaa', $parser->getNormalized());
    }
}
