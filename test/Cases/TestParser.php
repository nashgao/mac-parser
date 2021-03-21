<?php 

namespace Nashgao\Testing\Cases;


use Nashgao\Testing\AbstractTest;
use Nashgao\MacParser\MacAddress;

class TestParser extends AbstractTest
{
    public function testMac()
    {
        $mac = 'aa:aa:aa:aa:aa:aa';
        $parser = new MacAddress($mac);
        $norm = $parser->getNormalized();
        $this->assertTrue(true);
    }

}