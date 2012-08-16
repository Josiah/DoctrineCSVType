<?php namespace Doctrine\DBAL\Types;

use PHPUnit_Framework_TestCase as TestCase;
use Doctrine\Tests\DBAL\Mocks\MockPlatform as MockPlatform;

class CommaSeparatedValueTypeTest extends TestCase
{
    public function testConvertToDatabaseValue()
    {
        $type = Type::getType('csv');
        $platform = new MockPlatform();

        $this->assertNull($type->convertToDatabaseValue(null, $platform),
            "->convertToDatabaseValue() for null values");

        $this->assertEquals("",
            $type->convertToDatabaseValue(array(), $platform),
            "->convertToDatabaseValue() for empty arrays");

        $this->assertEquals('""',
            $type->convertToDatabaseValue(array(''), $platform),
            "->convertToDatabaseValue() for empty strings");

        $this->assertEquals('one,two,three',
            $type->convertToDatabaseValue(array('one','two','three'), $platform),
            "->convertToDatabaseValue() for simple values");

        $this->assertEquals('one,two three,"four, five","""six""",
                seven,eight',
            $type->convertToDatabaseValue(array('one','two three','four, five','"six"',"
                seven", 'eight'), $platform),
            "->convertToDatabaseValue() for values with delimter");
    }

    public function testConvertToPHPValue()
    {
        $type = Type::getType('csv');
        $platform = new MockPlatform();

        $this->assertNull($type->convertToPHPValue(null, $platform),
            "->convertToPHPValue() for null values");

        $this->assertEquals(array(),
            $type->convertToPHPValue("", $platform),
            "->convertToPHPValue() for empty arrays");

        $this->assertEquals(array('one','two','three'),
            $type->convertToPHPValue('one,two,three', $platform),
            "->convertToPHPValue() for simple values");

        $this->assertEquals(array('one','two three','four, five','"six"','
                seven'),
            $type->convertToPHPValue('one,two three,"four, five","""six""",
                seven', $platform),
            "->convertToPHPValue() for values with delimter");
    }
}
