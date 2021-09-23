<?php 

namespace Nashgao\Testing\Cases;


use Nashgao\MacParser\Exception\InvalidMacException;
use Nashgao\Testing\AbstractTest;
use Nashgao\MacParser\MacAddress;

class TestParser extends AbstractTest
{
    public function testMac()
    {
        $mac = 'aa:aa:aa:aa:aa:aa';
        $parser = new MacAddress($mac);
        $this->assertEquals('aaaaaaaaaaaa', $parser->getNormalized());

        $mac = 'aaaaaaaaaaaa';
        $parser = new MacAddress($mac);
        $this->assertEquals('aaaaaaaaaaaa', $parser->getNormalized());
    }

    public function testInvalidMac()
    {
        $invalidMac = 'aaa';
        try {
            $parser = new MacAddress($invalidMac, false);
        } catch (InvalidMacException $e) {
            $this->assertInstanceOf(InvalidMacException::class, $e);
        }
    }

    public function testReverseMac()
    {
        // test parse mac address from octets
        $octets = [
            10101010,
            10101010,
            10101010,
            10101010,
            10101010,
            10101010
        ];
        $parser = new MacAddress($octets);
        $this->assertEquals('aaaaaaaaaaaa', $parser->getNormalized());

        // test compliment
        $octets = [
            0101010,
            10101010,
            10101010,
            10101010,
            10101010,
            10101010
        ];
        $parser = new MacAddress($octets);
        $this->assertEquals('0aaaaaaaaaa', $parser->getNormalized());
    }

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

    public function testNumericMac()
    {
        $mac = 187649984473770;
        $parser = new MacAddress($mac);
        $this->assertEquals('aaaaaaaaaaaa', $parser->getNormalized());
    }
}
