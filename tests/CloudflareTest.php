<?php
namespace Codem\DomainValidation\Tests;

use Codem\DomainValidation\CloudflareDnsOverHttps;
use SilverStripe\Dev\SapphireTest;

/**
 * Tests for CloudflareDnsOverHttps
 */
class CloudflareTest extends SapphireTest
{
    private static $mx_domain = "codem.com.au";
    private static $mx_answer = "1 aspmx.l.google.com.";
    private static $a_domain = "codem.com.au";
    private static $a_answer = "codem.com.au";// no trailing dot

    public function setUp()
    {
        parent::setUp();
    }

    public function testNoMx()
    {
        $validator = new CloudflareDnsOverHttps();
        $validator->setDomain("test123.codem.com.au");
        $answers = $validator->performLookup('MX');
        // example.com should have no MX record
        $this->assertFalse($answers);
    }

    /**
     * Test that a domain has a specific MX record
     */
    public function testValidMxMatch()
    {
        $validator = new CloudflareDnsOverHttps();
        $validator->setDomain(self::$mx_domain);
        // Hopefully I don't change this regularly ;)
        $has = $validator->hasMxRecord(self::$mx_answer);
        $this->assertEquals($has, self::$mx_answer);
    }

    /**
     * Test that a domain has any MX records
     */
    public function testValidMxAny()
    {
        $validator = new CloudflareDnsOverHttps();
        $validator->setDomain(self::$mx_domain);
        $has = $validator->hasMxRecord();
        $this->assertTrue(is_array($has));
    }

    /**
     * Test that a domain has any A records
     */
    public function testValidARecord()
    {
        $validator = new CloudflareDnsOverHttps();
        $validator->setDomain(self::$a_domain);
        $answers = $validator->performLookup('A');
        $this->assertTrue(is_array($answers));

        $valid = false;
        foreach ($answers as $answer) {
            if (isset($answer->data) && $answer->name == self::$a_answer) {
                $valid = true;
            }
        }
        $this->assertTrue($valid);
    }

}
