<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\String\Tests\Unicode;

use ZerusTech\Component\String\Unicode\UTF32;

/**
 * Test case for UTF32.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class UTF32Test extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {

        $this->ref = new \ReflectionClass('ZerusTech\Component\String\Unicode\UTF32');

        $this->codespaceRange = $this->ref->getProperty('codespaceRange');
        $this->codespaceRange->setAccessible(true);

        $this->highSurrogateRange = $this->ref->getProperty('highSurrogateRange');
        $this->highSurrogateRange->setAccessible(true);

        $this->lowSurrogateRange = $this->ref->getProperty('lowSurrogateRange');
        $this->lowSurrogateRange->setAccessible(true);

        $this->noncharacters = $this->ref->getProperty('noncharacters');
        $this->noncharacters->setAccessible(true);

        $this->planes = $this->ref->getProperty('planes');
        $this->planes->setAccessible(true);

        $this->plainUTF8Patterns = $this->ref->getProperty('plainUTF8Patterns');
        $this->plainUTF8Patterns->setAccessible(true);

        $this->compiledUTF8Patterns = $this->ref->getProperty('compiledUTF8Patterns');
        $this->compiledUTF8Patterns->setAccessible(true);

        $this->privateUseAreaRange = $this->ref->getProperty('privateUseAreaRange');
        $this->privateUseAreaRange->setAccessible(true);

        $this->nonchars = range(0xfdd0, 0xfdef);

        foreach (range(0x00, 0x10) as $i) {

            $i = $i << 16;

            $this->nonchars[] = $i + 0xfffe;

            $this->nonchars[] = $i + 0xffff;
        }
    }

    public function tearDown()
    {
        $this->nonchars = null;
        $this->privateUseAreaRange = null;
        $this->compiledUTF8Patterns = null;
        $this->plainUTF8Patterns = null;
        $this->planes = null;
        $this->noncharacters = null;
        $this->lowSurrogateRange = null;
        $this->highSurrogateRange = null;
        $this->codespaceRange = null;
        $this->ref = null;
    }

    public function testConstants()
    {
        $this->assertEquals([0x000000, 0x10ffff], $this->codespaceRange->getValue());

        $this->assertEquals([0xd800, 0xdbff], $this->highSurrogateRange->getValue());

        $this->assertEquals([0xdc00, 0xdfff], $this->lowSurrogateRange->getValue());

        $this->assertEquals($this->nonchars, $this->noncharacters->getValue());

        $this->assertEquals([0xe000, 0xf8ff], $this->privateUseAreaRange->getValue());

        $this->assertEquals('9.0.0', UTF32::VERSION);
    }

    public function testNumberMethods()
    {
        $this->assertEquals(0x10ffff + 1, UTF32::numberOfCodePoints());

        $this->assertEquals(0xdbff - 0xd800 + 1, UTF32::numberOfHighSurrogateCodePoints());

        $this->assertEquals(0xdfff - 0xdc00 + 1, UTF32::numberOfLowSurrogateCodePoints());

        $this->assertEquals(0x10ffff + 1 - (0xdfff - 0xd800 + 1), UTF32::numberOfValidCodePoints());

        $this->assertEquals(0x10ffff + 1 - (0xdfff - 0xd800 + 1) - count($this->nonchars), UTF32::numberOfCharacterCodePoints());
    }

    /**
     * @dataProvider getDataForTestGetPlane
     */
    public function testGetPlane($index, $expected)
    {
        $spec = UTF32::getPlane($index);

        $this->assertEquals($expected, $spec);
    }

    public function getDataForTestGetPlane()
    {
        return [
            [0, ['id' => 'Plane 0', 'range' => [0x0000, 0xffff], 'name' => 'Basic Multilingual Plane', 'alias'=> 'BMP']],
            [1, ['id' => 'Plane 1', 'range' => [0x10000, 0x1ffff], 'name' => 'Supplementary Multilingual Plane', 'alias'=> 'SMP']],
            [2, ['id' => 'Plane 2', 'range' => [0x20000, 0x2ffff], 'name' => 'Supplementary Ideographic Plane', 'alias'=> 'SIP']],
            [14, ['id' => 'Plane 14', 'range' => [0xe0000, 0xeffff], 'name' => 'Supplementary Special-purpose Plane', 'alias'=> 'SSP']],
            [15, ['id' => 'Plane 15', 'range' => [0xf0000, 0xfffff], 'name' => 'Supplementary Private Use Area Plane A', 'alias'=> 'SPUA-A']],
            [16, ['id' => 'Plane 16', 'range' => [0x100000, 0x10ffff], 'name' => 'Supplementary Private Use Area Plane B', 'alias'=> 'SPUA-B']],
            [3, ['id' => 'Plane 3', 'range' => [0x30000, 0x3ffff], 'name' => null, 'alias'=> null]],
            [4, ['id' => 'Plane 4', 'range' => [0x40000, 0x4ffff], 'name' => null, 'alias'=> null]],
            [5, ['id' => 'Plane 5', 'range' => [0x50000, 0x5ffff], 'name' => null, 'alias'=> null]],
            [6, ['id' => 'Plane 6', 'range' => [0x60000, 0x6ffff], 'name' => null, 'alias'=> null]],
            [7, ['id' => 'Plane 7', 'range' => [0x70000, 0x7ffff], 'name' => null, 'alias'=> null]],
            [8, ['id' => 'Plane 8', 'range' => [0x80000, 0x8ffff], 'name' => null, 'alias'=> null]],
            [9, ['id' => 'Plane 9', 'range' => [0x90000, 0x9ffff], 'name' => null, 'alias'=> null]],
            [10, ['id' => 'Plane 10', 'range' => [0xa0000, 0xaffff], 'name' => null, 'alias'=> null]],
            [11, ['id' => 'Plane 11', 'range' => [0xb0000, 0xbffff], 'name' => null, 'alias'=> null]],
            [12, ['id' => 'Plane 12', 'range' => [0xc0000, 0xcffff], 'name' => null, 'alias'=> null]],
            [13, ['id' => 'Plane 13', 'range' => [0xd0000, 0xdffff], 'name' => null, 'alias'=> null]],
        ];
    }

    /**
     * @dataProvider getDataForTestConvertToUTF8
     */
    public function testConvertToUTF8($utf32, $expected)
    {
        $this->assertEquals($expected, UTF32::convertToUTF8($utf32));
    }

    public function getDataForTestConvertToUTF8()
    {
        return [
            [0x0024, '24'],
            [0x00a2, 'c2a2'],
            [0x20ac, 'e282ac'],
            [0x10437, 'f09090b7'],
        ];
    }

    /**
     * @dataProvider getDataForTestConvertToUTF16
     */
    public function testConvertToUTF16($utf32, $expected)
    {
        $this->assertEquals($expected, UTF32::convertToUTF16($utf32));
    }

    public function getDataForTestConvertToUTF16()
    {
        return [
            [0x0024, '0024'],
            [0x20ac, '20ac'],
            [0x10437, 'd801dc37'],
            [0x24b62, 'd852df62'],
        ];
    }
}
