<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\String\Unicode;

/**
 * This class represents the UTF32 encoding scheme.
 *
 * Overview
 * ---------
 *
 * Unicode defines a codespace of 1,114,112 code points in the range of 0x000000
 * to 0x10ffff.
 *
 * The unicode codespace is divided into 17 planes, numbered 0 to 16:
 *
 * Plane  0: 0x000000 - 0x00ffff / Basic Multilingual Plane / BMP
 * Plane  1: 0x010000 - 0x01ffff / Supplementary Multilingual Plane / SMP
 * Plane  2: 0x020000 - 0x02ffff / Supplementary Ideographic Plane / SIP
 * Plane 14: 0x0e0000 - 0x0effff / Supplementary Special-purpose Plane / SSP
 * Plane 15: 0x0f0000 - 0x0fffff / Supplementary Private Use Area Plane A / SPUA-A
 * Plane 16: 0x100000 - 0x10ffff / Supplementary Private Use Area Plane B / SPUA-B
 * Plane 3 - 13: 0x030000 - 0x0dffff / Unassigned
 *
 * Character General Category
 * ---------------------------
 *
 * Each code point has a single General Category property. The major categories
 * are: Letter, Mark, Number, Punction, Symbol and Other. Within these
 * categories, ther are subdivisions.
 *
 * Code points in the range 0xd800 - 0xdbff (1,024 code points) are known as the
 * high-surrogate, and code points in the range 0xdc00 - 0xdfff (1,024 code
 * points) are known as the low-surrogate. High and low surrogate code points
 * are not valid by themselves. Thus the range of code points that are available
 * for use as characters is 0x0000 - 0xd7ff and 0xe000 - 0x10ffff (111,206,4
 * code points).
 *
 * Certain noncharacter code points are guranteed never to be used for encoding
 * characters. There are 66 noncharacters: 0xfdd0 - 0xfdef and any code points
 * ending in the value fffe or ffff (i.e., 0xfffe, 0xffff, 0x1fffe, 0x1ffff).
 *
 * Thus the number of code points that are available for characters is 1,111,998.
 *
 * Reserved code points are those code points which are available for use as
 * encoded characters, but are not yet defined as characters by unicode.
 *
 * Private-use code points are considered to be assigned characters, but they
 * have no interpretation specified by the unicode stanadrd. There are three
 * private-use areas in the unicode codespace:
 *
 * - Private Use Area: 0xe000 - 0xf8ff (6,400 characters, inside the BMP plane).
 * - Supplementary Private Use Area-A: 0xf0000 - 0xffffd (65,534 characters)
 * - Supplementary Private Use Area-B: 0x100000 - 0x10fffd (65,534 characters)
 *
 * NOTE: The code points 0xffffe, 0xfffff, 0x10fffe and 0x10ffff are excluded
 * as "noncharacter code points".
 *
 * Graphic characters are those characters with a General Category other than
 * Cc, Cn, Co, Cs, Cf, Zl and Zp, that is to say ordinary visible characters
 * (including spaces with a non-zero width). There are 128,019 graphic
 * characters in unicode 9.0.0.
 *
 * Format characters are those characters with a General Category of 'Cf', 'Zl'
 * or 'Zp'. These are invisible characters defined by unicode for a particular
 * function. These include things like 0x200d (zero width joiner), 0x202d
 * (left-to-right override, interlinear annotation characters 0xfff9 - 0xfffb)
 * and the set of tag characters (0xe0001 and 0xe0020 - 0xe007f). They work
 * behind the scenes to do useful things like bidirectional control and
 * character shaping. There are 153 format characters in unicode 9.0.0.
 *
 * Sixty-five (65) code points (0x0000 - 0x001f and 0x007f - 0x009f) are
 * reserved as control codes.
 *
 * Graphic characters, format characters, control characters, and private
 * characters are known collectively as assigned characters.
 *
 * @link https://en.wikipedia.org/wiki/UTF-32 UTF-32
 * @link https://en.wikipedia.org/wiki/Unicode Unicode
 * @link http://www.babelstone.co.uk/Unicode/HowMany.html How many Characters
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class UTF32
{
    /**
     * The version of unicode stanard.
     */
    const VERSION = '9.0.0';

    /**
     * The range of unicode codespace.
     *
     * @var array The range of unicode codespace.
     */
    private static $codespaceRange = [0x000000, 0x10ffff];

    /**
     * The range of high surrogate code points.
     *
     * High surrogate code points are not valid, thus they can't be used as
     * characters.
     *
     * @var array The range of high surrogate code points.
     */
    private static $highSurrogateRange = [0xd800, 0xdbff];

    /**
     * The range of low surrogate code points.
     *
     * Low surrogate code points are not valid, thus they can't be used as
     * characters.
     *
     * @var array The range of low surrogate code points.
     */
    private static $lowSurrogateRange = [0xdc00, 0xdfff];

    /**
     * The 66 noncharacter code points are guaranteed never to be used for
     * encoding characters.
     *
     * 0xfdd0 - 0xfdef and 0fffe, 0ffff, 1fffe, 1ffff ...
     *
     * @var array The noncharacter code points.
     */
    private static $noncharacters = [
        0x00fdd0, 0x00fdd1, 0x00fdd2, 0x00fdd3, 0x00fdd4, 0x00fdd5, 0x00fdd6, 0x00fdd7,
        0x00fdd8, 0x00fdd9, 0x00fdda, 0x00fddb, 0x00fddc, 0x00fddd, 0x00fdde, 0x00fddf,
        0x00fde0, 0x00fde1, 0x00fde2, 0x00fde3, 0x00fde4, 0x00fde5, 0x00fde6, 0x00fde7,
        0x00fde8, 0x00fde9, 0x00fdea, 0x00fdeb, 0x00fdec, 0x00fded, 0x00fdee, 0x00fdef,
        0x00fffe, 0x00ffff, 0x01fffe, 0x01ffff, 0x02fffe, 0x02ffff, 0x03fffe, 0x03ffff,
        0x04fffe, 0x04ffff, 0x05fffe, 0x05ffff, 0x06fffe, 0x06ffff, 0x07fffe, 0x07ffff,
        0x08fffe, 0x08ffff, 0x09fffe, 0x09ffff, 0x0afffe, 0x0affff, 0x0bfffe, 0x0bffff,
        0x0cfffe, 0x0cffff, 0x0dfffe, 0x0dffff, 0x0efffe, 0x0effff, 0x0ffffe, 0x0fffff,
        0x10fffe, 0x10ffff,
    ];

    /**
     * The 65 code points are reserved as control codes.
     *
     * 0x0000 - 0x001f and 0x007f - 0x009f.
     *
     * @var array The control characters code points.
     */
    private static $controlCharacters = [
        0x0000, 0x0001, 0x0002, 0x0003, 0x0004, 0x0005, 0x0006, 0x0007,
        0x0008, 0x0009, 0x000a, 0x000b, 0x000c, 0x000d, 0x000e, 0x000f,
        0x0010, 0x0011, 0x0012, 0x0013, 0x0014, 0x0015, 0x0016, 0x0017,
        0x0018, 0x0019, 0x001a, 0x001b, 0x001c, 0x001d, 0x001e, 0x001f,
        0x007f,
        0x0080, 0x0081, 0x0082, 0x0083, 0x0084, 0x0085, 0x0086, 0x0087,
        0x0088, 0x0089, 0x008a, 0x008b, 0x008c, 0x008d, 0x008e, 0x008f,
        0x0090, 0x0091, 0x0092, 0x0093, 0x0094, 0x0095, 0x0096, 0x0097,
        0x0098, 0x0099, 0x009a, 0x009b, 0x009c, 0x009d, 0x009e, 0x009f,
    ];

    /**
     * List of unicode planes.
     *
     * @var array The list of unicode plane specifications.
     */
    private static $planes = [
        0 => ['id' => 'Plane 0', 'range' => [0x0000, 0xffff], 'name' => 'Basic Multilingual Plane', 'alias' => 'BMP', ],
        1 => ['id' => 'Plane 1', 'range' => [0x10000, 0x1ffff], 'name' => 'Supplementary Multilingual Plane', 'alias' => 'SMP', ],
        2 => ['id' => 'Plane 2', 'range' => [0x20000, 0x2ffff], 'name' => 'Supplementary Ideographic Plane', 'alias' => 'SIP', ],
        14 => ['id' => 'Plane 14', 'range' => [0xe0000, 0xeffff], 'name' => 'Supplementary Special-purpose Plane', 'alias' => 'SSP', ],
        15 => ['id' => 'Plane 15', 'range' => [0xf0000, 0xfffff], 'name' => 'Supplementary Private Use Area Plane A', 'alias' => 'SPUA-A', ],
        16 => ['id' => 'Plane 16', 'range' => [0x100000, 0x10ffff], 'name' => 'Supplementary Private Use Area Plane B', 'alias' => 'SPUA-B', ],
        3 => ['id' => 'Plane 3', 'range' => [0x30000, 0x3ffff], 'name' => null, 'alias' => null, ],
        4 => ['id' => 'Plane 4', 'range' => [0x40000, 0x4ffff], 'name' => null, 'alias' => null, ],
        5 => ['id' => 'Plane 5', 'range' => [0x50000, 0x5ffff], 'name' => null, 'alias' => null, ],
        6 => ['id' => 'Plane 6', 'range' => [0x60000, 0x6ffff], 'name' => null, 'alias' => null, ],
        7 => ['id' => 'Plane 7', 'range' => [0x70000, 0x7ffff], 'name' => null, 'alias' => null, ],
        8 => ['id' => 'Plane 8', 'range' => [0x80000, 0x8ffff], 'name' => null, 'alias' => null, ],
        9 => ['id' => 'Plane 9', 'range' => [0x90000, 0x9ffff], 'name' => null, 'alias' => null, ],
        10 => ['id' => 'Plane 10', 'range' => [0xa0000, 0xaffff], 'name' => null, 'alias' => null, ],
        11 => ['id' => 'Plane 11', 'range' => [0xb0000, 0xbffff], 'name' => null, 'alias' => null, ],
        12 => ['id' => 'Plane 12', 'range' => [0xc0000, 0xcffff], 'name' => null, 'alias' => null, ],
        13 => ['id' => 'Plane 13', 'range' => [0xd0000, 0xdffff], 'name' => null, 'alias' => null, ],
    ];

    /**
     * Range of the private use area.
     *
     * @var array The range of the private use area.
     */
    private static $privateUseAreaRange = [0xe000, 0xf8ff];

    /**
     * The pattern for converting utf32 to utf8.
     *
     * @link https://en.wikipedia.org/wiki/UTF-8 UTF-8.
     * @var array The patterns for converting utf32 to utf8.
     */
    private static $plainUTF8Patterns = [
        [0x0000, 0x007f, '0xxxxxxx'],
        [0x0080, 0x07ff, '110xxxxx 10xxxxxx'],
        [0x0800, 0xffff, '1110xxxx 10xxxxxx 10xxxxxx'],
        [0x10000, 0x10ffff, '11110xxx 10xxxxxx 10xxxxxx 10xxxxxx']
    ];

    /**
     * The compiled patterns for converting utf32 to utf8:
     *
     *     [
     *         [
     *             'utf32_from' => 0x0000,
     *             'utf32_to' => 0x007f,
     *             'utf8_code_patterns' => [
     *                 [
     *                     'prefix' => '0',
     *                     'no_of_code_bits' => 7,
     *                 ],
     *                 ...
     *             ]
     *             'total_no_of_code_bits' => 7,
     *         ],
     *         ...
     *     ]
     *
     * @var array Compiled patterns for converting utf32 to utf8.
     */
    private static $compiledUTF8Patterns = null;

    /**
     * Returns the number of all possible code points, including surrogate code
     * points and noncharacter code points,  in the unicode code space.
     *
     * @return int The number of all code points.
     */
    public static function numberOfCodePoints()
    {
        return static::$codespaceRange[1] - static::$codespaceRange[0] + 1;
    }

    /**
     * Returns the number of high surrogate code points.
     *
     * @return int The number of high surrogate code points.
     */
    public static function numberOfHighSurrogateCodePoints()
    {
        return static::$highSurrogateRange[1] - static::$highSurrogateRange[0] + 1;
    }

    /**
     * Returns the number of low surrogate code points.
     *
     * @return int The number of low surrogate code points.
     */
    public static function numberOfLowSurrogateCodePoints()
    {
        return static::$lowSurrogateRange[1] - static::$lowSurrogateRange[0] + 1;
    }

    /**
     * Returns the number of all valid code points, all surrogate code points are
     * excluded.
     *
     * @return int The number of valid code points.
     */
    public static function numberOfValidCodePoints()
    {
        return static::numberOfCodePoints() - static::numberOfHighSurrogateCodePoints() - static::numberOfLowSurrogateCodePoints();
    }

    /**
     * Returns the number of noncharacter code points.
     *
     * @return int The number of noncharacter code points.
     */
    public static function numberOfNoncharacterCodePoints()
    {
        return count(static::$noncharacters);
    }

    /**
     * Returns the number of code points that can be used to encode characters.
     *
     * @return int The number of character code points.
     */
    public static function numberOfCharacterCodePoints()
    {
        return static::numberOfValidCodePoints() - static::numberOfNoncharacterCodePoints();
    }

    /**
     * Returns the plane specificaitons of the given index.
     *
     * @param int $index Index of the plane.
     * @return array The plane specifications.
     * @throws \OutOfBoundsException If no plane of the given index can be
     * found.
     */
    public static function getPlane($index)
    {
        if (!array_key_exists($index, static::$planes)) {

            throw new \OutOfBoundsException(sprintf("No plane of index: %d can be found.", $index));
        }

        return static::$planes[$index];
    }

    /**
     * Converts the provided utf32 code (integer value) to utf8 code
     * (hexadecimal string).
     *
     * @link https://en.wikipedia.org/wiki/UTF-8 UTF-8
     * @param int $utf32 The utf32 code.
     * @return string The utf8 code as a hexadecimal string.
     */
    public static function convertToUTF8($utf32)
    {
        $index = 0;

        $compiledUTF8Pattern = null;

        static::compileUTF8Patterns();

        foreach (static::$compiledUTF8Patterns as $pattern) {

            if ($utf32 >= $pattern['utf32_from'] && $utf32 <= $pattern['utf32_to']) {

                $compiledUTF8Pattern = $pattern;

                break;
            }
        }

        $utf32Code = str_pad(decbin($utf32), $compiledUTF8Pattern['total_no_of_code_bits'], '0', STR_PAD_LEFT);

        $offset = 0;

        $utf8CodePatterns = $compiledUTF8Pattern['utf8_code_patterns'];

        $hex = '';

        foreach ($utf8CodePatterns as $pattern) {

            $code = $pattern['prefix'].substr($utf32Code, $offset, $pattern['no_of_code_bits']);

            $hex .= dechex(bindec($code));

            $offset += $pattern['no_of_code_bits'];
        }

        $hex = str_pad($hex, 2, '0', STR_PAD_LEFT);

        return $hex;
    }

    /**
     * Converts the provided utf32 code (integer value) to utf16 code
     * (hexadecimal string).
     *
     * @link https://en.wikipedia.org/wiki/UTF-16 UTF-16
     *
     * @param int $utf32 The utf32 code.
     * @return string The utf16 code as a hexadecimal string.
     */
    public static function convertToUTF16($utf32)
    {
        $hex = '';

        if ($utf32 >= 0x0000 && $utf32 <= 0xd7ff || $utf32 >= 0xe000 && $utf32 <= 0xffff) {

            $hex = str_pad(dechex($utf32), 4, '0', STR_PAD_LEFT);

        } else if ($utf32 >= 0x10000 && $utf32 <= 0x10ffff) {

            $utf8Code = $utf32 - 0x10000;

            $utf8Bytes = str_pad(decbin($utf8Code),20, '0', STR_PAD_LEFT);

            $highSurrogate = dechex(bindec(substr($utf8Bytes, 0, 10)) + 0xd800);

            $lowSurrogate = dechex(bindec(substr($utf8Bytes, 10)) + 0xdc00);

            $hex = $highSurrogate.$lowSurrogate;
        }

        return $hex;
    }

    /**
     * Compiles all utf32 to utf8 patterns.
     */
    protected static function compileUTF8Patterns()
    {
        if (null === static::$compiledUTF8Patterns) {

            static::$compiledUTF8Patterns = [];

            foreach (static::$plainUTF8Patterns as $pattern) {

                static::$compiledUTF8Patterns[] = static::compileUTF8Pattern($pattern);
            }
        }
    }

    /**
     * Compiles a single utf32 to utf8 pattern:
     *
     * original pattern:
     *
     *     [0x0000, 0x007f, '0xxxxxxx']
     *
     * compiled pattern:
     *
     *     [
     *         'utf32_from' => 0x0000,
     *         'utf32_to' => 0x007f,
     *         'utf8_code_patterns' => [
     *             [
     *                 'prefix' => '0',
     *                 'no_of_code_bits' => 7,
     *             ],
     *             ...
     *         ]
     *         'total_no_of_code_bits' => 7,
     *     ]
     *
     * @param array The original utf32 to utf8 pattern
     * @return array The compiled pattern
     */
    protected static function compileUTF8Pattern($pattern)
    {
        $compiled = [];

        $compiled['utf32_from'] = $pattern[0];

        $compiled['utf32_to'] = $pattern[1];

        $utf8PatternBytes = explode(' ', $pattern[2]);

        $totalNoOfCodeBits = 0;

        $utf8CodePatterns = [];

        for ($i = 0; $i < count($utf8PatternBytes); $i++) {

            $byte = $utf8PatternBytes[$i];

            $pos = strpos($byte, 'x');

            $noOfCodeBits = strlen(substr($byte, $pos));

            $totalNoOfCodeBits += $noOfCodeBits;

            $utf8CodePatterns[] = [
                'prefix' => substr($byte, 0, $pos),
                'no_of_code_bits' => $noOfCodeBits,
            ];
        }

        $compiled['total_no_of_code_bits'] = $totalNoOfCodeBits;

        $compiled['utf8_code_patterns'] = $utf8CodePatterns;

        return $compiled;
    }
}
