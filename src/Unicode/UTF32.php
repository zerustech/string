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
 * @link https://en.wikipedia.org/wiki/UTF-32 UTF-32
 * @link https://en.wikipedia.org/wiki/Unicode Unicode
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class UTF32
{
    /**
     * The range of unicode code space.
     */
    const CODESPACE_RANGE = [0x000000, 0x10ffff];

    /**
     * The range of high surrogate code points.
     *
     * High surrogate code points are not valid, thus they can't be used as
     * characters.
     */
    const HIGH_SURROGATE_RANGE = [0xd800, 0xdbff];

    /**
     * The range of low surrogate code points.
     *
     * Low surrogate code points are not valid, thus they can't be used as
     * characters.
     */
    const LOW_SURROGATE_RANGE = [0xdc00, 0xdfff];

    /**
     * The 66 noncharacter code points are guaranteed never to be used for
     * encoding characters.
     */
    const NONCHARACTER_LIST = [
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
     * List of unicode planes.
     */
    const PLANE_SPECIFICATION_LIST = [
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
     * The pattern for converting utf32 to utf8.
     *
     * @link https://en.wikipedia.org/wiki/UTF-8 UTF-8.
     */
    const UTF8_PATTERNS = [
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
     * @var array Compiled patterns for converting utf32 to utf8.
     */
    static $compiledUTF8Patterns = null;

    /**
     * Returns the number of all possible code points, including surrogate code
     * points and noncharacter code points,  in the unicode code space.
     *
     * @return int The number of all code points.
     */
    public static function numberOfCodePoints()
    {
        return static::CODESPACE_RANGE[1] - static::CODESPACE_RANGE[0] + 1;
    }

    /**
     * Returns the number of high surrogate code points.
     *
     * @return int The number of high surrogate code points.
     */
    public static function numberOfHighSurrogateCodePoints()
    {
        return static::HIGH_SURROGATE_RANGE[1] - static::HIGH_SURROGATE_RANGE[0] + 1;
    }

    /**
     * Returns the number of low surrogate code points.
     *
     * @return int The number of low surrogate code points.
     */
    public static function numberOfLowSurrogateCodePoints()
    {
        return static::LOW_SURROGATE_RANGE[1] - static::LOW_SURROGATE_RANGE[0] + 1;
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
    public static function numberOfNonCharacterCodePoints()
    {
        return count(static::NONCHARACTER_LIST);
    }

    /**
     * Returns the number of code points that can be used to encode characters.
     *
     * @return int The number of character code points.
     */
    public static function numberOfCharacterCodePoints()
    {
        return static::numberOfValidCodePoints() - static::numberOfNonCharacterCodePoints();
    }

    /**
     * Returns the plane specificaitons of the given index.
     *
     * @param int $index Index of the plane.
     * @return array The plane specifications.
     * @throws \OutOfBoundsException If no plane of the given index can be
     * found.
     */
    public static function getPlaneSpecifications($index)
    {
        if (!array_key_exists($index, static::PLANE_SPECIFICATION_LIST)) {

            throw new \OutOfBoundsException(sprintf("No plane of index: %d can be found.", $index));
        }

        return static::PLANE_SPECIFICATION_LIST[$index];
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

            foreach (static::UTF8_PATTERNS as $pattern) {

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
