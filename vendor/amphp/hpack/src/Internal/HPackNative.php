<?php

namespace Amp\Http\Internal;

use Amp\Http\HPackException;
/** @internal */
final class HPackNative
{
    private const HUFFMAN_CODE = array(0 => 8184, 1 => 8388568, 2 => 268435426, 3 => 268435427, 4 => 268435428, 5 => 268435429, 6 => 268435430, 7 => 268435431, 8 => 268435432, 9 => 16777194, 10 => 1073741820, 11 => 268435433, 12 => 268435434, 13 => 1073741821, 14 => 268435435, 15 => 268435436, 16 => 268435437, 17 => 268435438, 18 => 268435439, 19 => 268435440, 20 => 268435441, 21 => 268435442, 22 => 1073741822, 23 => 268435443, 24 => 268435444, 25 => 268435445, 26 => 268435446, 27 => 268435447, 28 => 268435448, 29 => 268435449, 30 => 268435450, 31 => 268435451, 32 => 20, 33 => 1016, 34 => 1017, 35 => 4090, 36 => 8185, 37 => 21, 38 => 248, 39 => 2042, 40 => 1018, 41 => 1019, 42 => 249, 43 => 2043, 44 => 250, 45 => 22, 46 => 23, 47 => 24, 48 => 0, 49 => 1, 50 => 2, 51 => 25, 52 => 26, 53 => 27, 54 => 28, 55 => 29, 56 => 30, 57 => 31, 58 => 92, 59 => 251, 60 => 32764, 61 => 32, 62 => 4091, 63 => 1020, 64 => 8186, 65 => 33, 66 => 93, 67 => 94, 68 => 95, 69 => 96, 70 => 97, 71 => 98, 72 => 99, 73 => 100, 74 => 101, 75 => 102, 76 => 103, 77 => 104, 78 => 105, 79 => 106, 80 => 107, 81 => 108, 82 => 109, 83 => 110, 84 => 111, 85 => 112, 86 => 113, 87 => 114, 88 => 252, 89 => 115, 90 => 253, 91 => 8187, 92 => 524272, 93 => 8188, 94 => 16380, 95 => 34, 96 => 32765, 97 => 3, 98 => 35, 99 => 4, 100 => 36, 101 => 5, 102 => 37, 103 => 38, 104 => 39, 105 => 6, 106 => 116, 107 => 117, 108 => 40, 109 => 41, 110 => 42, 111 => 7, 112 => 43, 113 => 118, 114 => 44, 115 => 8, 116 => 9, 117 => 45, 118 => 119, 119 => 120, 120 => 121, 121 => 122, 122 => 123, 123 => 32766, 124 => 2044, 125 => 16381, 126 => 8189, 127 => 268435452, 128 => 1048550, 129 => 4194258, 130 => 1048551, 131 => 1048552, 132 => 4194259, 133 => 4194260, 134 => 4194261, 135 => 8388569, 136 => 4194262, 137 => 8388570, 138 => 8388571, 139 => 8388572, 140 => 8388573, 141 => 8388574, 142 => 16777195, 143 => 8388575, 144 => 16777196, 145 => 16777197, 146 => 4194263, 147 => 8388576, 148 => 16777198, 149 => 8388577, 150 => 8388578, 151 => 8388579, 152 => 8388580, 153 => 2097116, 154 => 4194264, 155 => 8388581, 156 => 4194265, 157 => 8388582, 158 => 8388583, 159 => 16777199, 160 => 4194266, 161 => 2097117, 162 => 1048553, 163 => 4194267, 164 => 4194268, 165 => 8388584, 166 => 8388585, 167 => 2097118, 168 => 8388586, 169 => 4194269, 170 => 4194270, 171 => 16777200, 172 => 2097119, 173 => 4194271, 174 => 8388587, 175 => 8388588, 176 => 2097120, 177 => 2097121, 178 => 4194272, 179 => 2097122, 180 => 8388589, 181 => 4194273, 182 => 8388590, 183 => 8388591, 184 => 1048554, 185 => 4194274, 186 => 4194275, 187 => 4194276, 188 => 8388592, 189 => 4194277, 190 => 4194278, 191 => 8388593, 192 => 67108832, 193 => 67108833, 194 => 1048555, 195 => 524273, 196 => 4194279, 197 => 8388594, 198 => 4194280, 199 => 33554412, 200 => 67108834, 201 => 67108835, 202 => 67108836, 203 => 134217694, 204 => 134217695, 205 => 67108837, 206 => 16777201, 207 => 33554413, 208 => 524274, 209 => 2097123, 210 => 67108838, 211 => 134217696, 212 => 134217697, 213 => 67108839, 214 => 134217698, 215 => 16777202, 216 => 2097124, 217 => 2097125, 218 => 67108840, 219 => 67108841, 220 => 268435453, 221 => 134217699, 222 => 134217700, 223 => 134217701, 224 => 1048556, 225 => 16777203, 226 => 1048557, 227 => 2097126, 228 => 4194281, 229 => 2097127, 230 => 2097128, 231 => 8388595, 232 => 4194282, 233 => 4194283, 234 => 33554414, 235 => 33554415, 236 => 16777204, 237 => 16777205, 238 => 67108842, 239 => 8388596, 240 => 67108843, 241 => 134217702, 242 => 67108844, 243 => 67108845, 244 => 134217703, 245 => 134217704, 246 => 134217705, 247 => 134217706, 248 => 134217707, 249 => 268435454, 250 => 134217708, 251 => 134217709, 252 => 134217710, 253 => 134217711, 254 => 134217712, 255 => 67108846, 256 => 1073741823);
    private const HUFFMAN_CODE_LENGTHS = array(0 => 13, 1 => 23, 2 => 28, 3 => 28, 4 => 28, 5 => 28, 6 => 28, 7 => 28, 8 => 28, 9 => 24, 10 => 30, 11 => 28, 12 => 28, 13 => 30, 14 => 28, 15 => 28, 16 => 28, 17 => 28, 18 => 28, 19 => 28, 20 => 28, 21 => 28, 22 => 30, 23 => 28, 24 => 28, 25 => 28, 26 => 28, 27 => 28, 28 => 28, 29 => 28, 30 => 28, 31 => 28, 32 => 6, 33 => 10, 34 => 10, 35 => 12, 36 => 13, 37 => 6, 38 => 8, 39 => 11, 40 => 10, 41 => 10, 42 => 8, 43 => 11, 44 => 8, 45 => 6, 46 => 6, 47 => 6, 48 => 5, 49 => 5, 50 => 5, 51 => 6, 52 => 6, 53 => 6, 54 => 6, 55 => 6, 56 => 6, 57 => 6, 58 => 7, 59 => 8, 60 => 15, 61 => 6, 62 => 12, 63 => 10, 64 => 13, 65 => 6, 66 => 7, 67 => 7, 68 => 7, 69 => 7, 70 => 7, 71 => 7, 72 => 7, 73 => 7, 74 => 7, 75 => 7, 76 => 7, 77 => 7, 78 => 7, 79 => 7, 80 => 7, 81 => 7, 82 => 7, 83 => 7, 84 => 7, 85 => 7, 86 => 7, 87 => 7, 88 => 8, 89 => 7, 90 => 8, 91 => 13, 92 => 19, 93 => 13, 94 => 14, 95 => 6, 96 => 15, 97 => 5, 98 => 6, 99 => 5, 100 => 6, 101 => 5, 102 => 6, 103 => 6, 104 => 6, 105 => 5, 106 => 7, 107 => 7, 108 => 6, 109 => 6, 110 => 6, 111 => 5, 112 => 6, 113 => 7, 114 => 6, 115 => 5, 116 => 5, 117 => 6, 118 => 7, 119 => 7, 120 => 7, 121 => 7, 122 => 7, 123 => 15, 124 => 11, 125 => 14, 126 => 13, 127 => 28, 128 => 20, 129 => 22, 130 => 20, 131 => 20, 132 => 22, 133 => 22, 134 => 22, 135 => 23, 136 => 22, 137 => 23, 138 => 23, 139 => 23, 140 => 23, 141 => 23, 142 => 24, 143 => 23, 144 => 24, 145 => 24, 146 => 22, 147 => 23, 148 => 24, 149 => 23, 150 => 23, 151 => 23, 152 => 23, 153 => 21, 154 => 22, 155 => 23, 156 => 22, 157 => 23, 158 => 23, 159 => 24, 160 => 22, 161 => 21, 162 => 20, 163 => 22, 164 => 22, 165 => 23, 166 => 23, 167 => 21, 168 => 23, 169 => 22, 170 => 22, 171 => 24, 172 => 21, 173 => 22, 174 => 23, 175 => 23, 176 => 21, 177 => 21, 178 => 22, 179 => 21, 180 => 23, 181 => 22, 182 => 23, 183 => 23, 184 => 20, 185 => 22, 186 => 22, 187 => 22, 188 => 23, 189 => 22, 190 => 22, 191 => 23, 192 => 26, 193 => 26, 194 => 20, 195 => 19, 196 => 22, 197 => 23, 198 => 22, 199 => 25, 200 => 26, 201 => 26, 202 => 26, 203 => 27, 204 => 27, 205 => 26, 206 => 24, 207 => 25, 208 => 19, 209 => 21, 210 => 26, 211 => 27, 212 => 27, 213 => 26, 214 => 27, 215 => 24, 216 => 21, 217 => 21, 218 => 26, 219 => 26, 220 => 28, 221 => 27, 222 => 27, 223 => 27, 224 => 20, 225 => 24, 226 => 20, 227 => 21, 228 => 22, 229 => 21, 230 => 21, 231 => 23, 232 => 22, 233 => 22, 234 => 25, 235 => 25, 236 => 24, 237 => 24, 238 => 26, 239 => 23, 240 => 26, 241 => 27, 242 => 26, 243 => 26, 244 => 27, 245 => 27, 246 => 27, 247 => 27, 248 => 27, 249 => 28, 250 => 27, 251 => 27, 252 => 27, 253 => 27, 254 => 27, 255 => 26, 256 => 30);
    private const DEFAULT_COMPRESSION_THRESHOLD = 1024;
    private const DEFAULT_MAX_SIZE = 4096;
    private static $huffmanLookup;
    private static $huffmanCodes;
    private static $huffmanLengths;
    private static $indexMap = [];
    /** @var string[][] */
    private $headers = [];
    /** @var int */
    private $hardMaxSize = self::DEFAULT_MAX_SIZE;
    /** @var int Max table size. */
    private $currentMaxSize = self::DEFAULT_MAX_SIZE;
    /** @var int Current table size. */
    private $size = 0;
    /** Called via bindTo(), see end of file */
    private static function init()
    {
        self::$huffmanLookup = self::huffmanLookupInit();
        self::$huffmanCodes = self::huffmanCodesInit();
        self::$huffmanLengths = self::huffmanLengthsInit();
        foreach (\array_column(self::TABLE, 0) as $index => $name) {
            if (isset(self::$indexMap[$name])) {
                continue;
            }
            self::$indexMap[$name] = $index + 1;
        }
    }
    // (micro-)optimized decode
    private static function huffmanLookupInit() : array
    {
        if ('cli' !== \PHP_SAPI && 'phpdbg' !== \PHP_SAPI || \filter_var(\ini_get('opcache.enable_cli'), \FILTER_VALIDATE_BOOLEAN)) {
            return require __DIR__ . '/huffman-lookup.php';
        }
        \gc_disable();
        $encodingAccess = [];
        $terminals = [];
        $index = 7;
        foreach (self::HUFFMAN_CODE as $chr => $bits) {
            $len = self::HUFFMAN_CODE_LENGTHS[$chr];
            for ($bit = 0; $bit < 8; $bit++) {
                $offlen = $len + $bit;
                $next = $bit;
                for ($byte = $offlen - 1 >> 3; $byte > 0; $byte--) {
                    $cur = \str_pad(\decbin($bits >> $byte * 8 - (0x30 - $offlen & 7) & 0xff), 8, "0", STR_PAD_LEFT);
                    if (($encodingAccess[$next][$cur][0] ?? 0) !== 0) {
                        $next = $encodingAccess[$next][$cur][0];
                    } else {
                        $encodingAccess[$next][$cur] = [++$index, null];
                        $next = $index;
                    }
                }
                $key = \str_pad(\decbin($bits & (1 << ($offlen - 1 & 7) + 1) - 1), ($offlen - 1 & 7) + 1, "0", STR_PAD_LEFT);
                $encodingAccess[$next][$key] = [null, $chr > 0xff ? "" : \chr($chr)];
                if ($offlen & 7) {
                    $terminals[$offlen & 7][] = [$key, $next];
                } else {
                    $encodingAccess[$next][$key][0] = 0;
                }
            }
        }
        $memoize = [];
        for ($off = 7; $off > 0; $off--) {
            foreach ($terminals[$off] as [$key, $next]) {
                if ($encodingAccess[$next][$key][0] === null) {
                    foreach ($encodingAccess[$off] as $chr => $cur) {
                        $encodingAccess[$next][($memoize[$key] ?? ($memoize[$key] = \str_pad($key, 8, "0", STR_PAD_RIGHT))) | $chr] = [$cur[0], $encodingAccess[$next][$key][1] != "" ? $encodingAccess[$next][$key][1] . $cur[1] : ""];
                    }
                    unset($encodingAccess[$next][$key]);
                }
            }
        }
        $memoize = [];
        for ($off = 7; $off > 0; $off--) {
            foreach ($terminals[$off] as [$key, $next]) {
                foreach ($encodingAccess[$next] as $k => $v) {
                    if (\strlen($k) !== 1) {
                        $encodingAccess[$next][$memoize[$k] ?? ($memoize[$k] = \chr(\bindec($k)))] = $v;
                        unset($encodingAccess[$next][$k]);
                    }
                }
            }
            unset($encodingAccess[$off]);
        }
        \gc_enable();
        return $encodingAccess;
    }
    /**
     * @param string $input
     *
     * @return string|null Returns null if decoding fails.
     */
    public static function huffmanDecode(string $input)
    {
        $huffmanLookup = self::$huffmanLookup;
        $lookup = 0;
        $lengths = self::$huffmanLengths;
        $length = \strlen($input);
        $out = \str_repeat("\x00", (int) \floor($length / 5 * 8 + 1));
        // max length
        // Fail if EOS symbol is found.
        if (\strpos($input, "?\xff\xff\xff") !== false) {
            return null;
        }
        for ($bitCount = $off = $i = 0; $i < $length; $i++) {
            [$lookup, $chr] = $huffmanLookup[$lookup][$input[$i]];
            if ($chr === null) {
                continue;
            }
            if ($chr === "") {
                return null;
            }
            $out[$off++] = $chr[0];
            $bitCount += $lengths[$chr[0]];
            if (isset($chr[1])) {
                $out[$off++] = $chr[1];
                $bitCount += $lengths[$chr[1]];
            }
        }
        // Padding longer than 7-bits
        if ($i && $chr === null) {
            return null;
        }
        // Check for 0's in padding
        if ($bitCount & 7) {
            $mask = 0xff >> ($bitCount & 7);
            if ((\ord($input[$i - 1]) & $mask) !== $mask) {
                return null;
            }
        }
        return \substr($out, 0, $off);
    }
    private static function huffmanCodesInit() : array
    {
        if ('cli' !== \PHP_SAPI && 'phpdbg' !== \PHP_SAPI || \filter_var(\ini_get('opcache.enable_cli'), \FILTER_VALIDATE_BOOLEAN)) {
            return require __DIR__ . '/huffman-codes.php';
        }
        $lookup = [];
        for ($chr = 0; $chr <= 0xff; $chr++) {
            $bits = self::HUFFMAN_CODE[$chr];
            $length = self::HUFFMAN_CODE_LENGTHS[$chr];
            for ($bit = 0; $bit < 8; $bit++) {
                $bytes = $length + $bit - 1 >> 3;
                $codes = [];
                for ($byte = $bytes; $byte >= 0; $byte--) {
                    $codes[] = \chr($byte ? $bits >> $length - ($bytes - $byte + 1) * 8 + $bit : $bits << (0x30 - $length - $bit & 7));
                }
                $lookup[$bit][\chr($chr)] = $codes;
            }
        }
        return $lookup;
    }
    private static function huffmanLengthsInit() : array
    {
        $lengths = [];
        for ($chr = 0; $chr <= 0xff; $chr++) {
            $lengths[\chr($chr)] = self::HUFFMAN_CODE_LENGTHS[$chr];
        }
        return $lengths;
    }
    public static function huffmanEncode(string $input) : string
    {
        $codes = self::$huffmanCodes;
        $lengths = self::$huffmanLengths;
        $length = \strlen($input);
        $out = \str_repeat("\x00", $length * 5 + 1);
        // max length
        for ($bitCount = $i = 0; $i < $length; $i++) {
            $chr = $input[$i];
            $byte = $bitCount >> 3;
            foreach ($codes[$bitCount & 7][$chr] as $bits) {
                // Note: |= can't be used with strings in PHP
                $out[$byte] = $out[$byte] | $bits;
                $byte++;
            }
            $bitCount += $lengths[$chr];
        }
        if ($bitCount & 7) {
            // Note: |= can't be used with strings in PHP
            $out[$byte - 1] = $out[$byte - 1] | \chr(0xff >> ($bitCount & 7));
        }
        return $i ? \substr($out, 0, $byte) : '';
    }
    /** @see RFC 7541 Appendix A */
    const LAST_INDEX = 61;
    const TABLE = array(0 => array(0 => ':authority', 1 => ''), 1 => array(0 => ':method', 1 => 'GET'), 2 => array(0 => ':method', 1 => 'POST'), 3 => array(0 => ':path', 1 => '/'), 4 => array(0 => ':path', 1 => '/index.html'), 5 => array(0 => ':scheme', 1 => 'http'), 6 => array(0 => ':scheme', 1 => 'https'), 7 => array(0 => ':status', 1 => '200'), 8 => array(0 => ':status', 1 => '204'), 9 => array(0 => ':status', 1 => '206'), 10 => array(0 => ':status', 1 => '304'), 11 => array(0 => ':status', 1 => '400'), 12 => array(0 => ':status', 1 => '404'), 13 => array(0 => ':status', 1 => '500'), 14 => array(0 => 'accept-charset', 1 => ''), 15 => array(0 => 'accept-encoding', 1 => 'gzip, deflate'), 16 => array(0 => 'accept-language', 1 => ''), 17 => array(0 => 'accept-ranges', 1 => ''), 18 => array(0 => 'accept', 1 => ''), 19 => array(0 => 'access-control-allow-origin', 1 => ''), 20 => array(0 => 'age', 1 => ''), 21 => array(0 => 'allow', 1 => ''), 22 => array(0 => 'authorization', 1 => ''), 23 => array(0 => 'cache-control', 1 => ''), 24 => array(0 => 'content-disposition', 1 => ''), 25 => array(0 => 'content-encoding', 1 => ''), 26 => array(0 => 'content-language', 1 => ''), 27 => array(0 => 'content-length', 1 => ''), 28 => array(0 => 'content-location', 1 => ''), 29 => array(0 => 'content-range', 1 => ''), 30 => array(0 => 'content-type', 1 => ''), 31 => array(0 => 'cookie', 1 => ''), 32 => array(0 => 'date', 1 => ''), 33 => array(0 => 'etag', 1 => ''), 34 => array(0 => 'expect', 1 => ''), 35 => array(0 => 'expires', 1 => ''), 36 => array(0 => 'from', 1 => ''), 37 => array(0 => 'host', 1 => ''), 38 => array(0 => 'if-match', 1 => ''), 39 => array(0 => 'if-modified-since', 1 => ''), 40 => array(0 => 'if-none-match', 1 => ''), 41 => array(0 => 'if-range', 1 => ''), 42 => array(0 => 'if-unmodified-since', 1 => ''), 43 => array(0 => 'last-modified', 1 => ''), 44 => array(0 => 'link', 1 => ''), 45 => array(0 => 'location', 1 => ''), 46 => array(0 => 'max-forwards', 1 => ''), 47 => array(0 => 'proxy-authentication', 1 => ''), 48 => array(0 => 'proxy-authorization', 1 => ''), 49 => array(0 => 'range', 1 => ''), 50 => array(0 => 'referer', 1 => ''), 51 => array(0 => 'refresh', 1 => ''), 52 => array(0 => 'retry-after', 1 => ''), 53 => array(0 => 'server', 1 => ''), 54 => array(0 => 'set-cookie', 1 => ''), 55 => array(0 => 'strict-transport-security', 1 => ''), 56 => array(0 => 'transfer-encoding', 1 => ''), 57 => array(0 => 'user-agent', 1 => ''), 58 => array(0 => 'vary', 1 => ''), 59 => array(0 => 'via', 1 => ''), 60 => array(0 => 'www-authenticate', 1 => ''));
    private static function decodeDynamicInteger(string $input, int &$off) : int
    {
        if (!isset($input[$off])) {
            throw new HPackException('Invalid input data, too short for dynamic integer');
        }
        $c = \ord($input[$off++]);
        $int = $c & 0x7f;
        $i = 0;
        while ($c & 0x80) {
            if (!isset($input[$off])) {
                return -0x80;
            }
            $c = \ord($input[$off++]);
            $int += ($c & 0x7f) << ++$i * 7;
        }
        return $int;
    }
    /**
     * @param int $maxSize Upper limit on table size.
     */
    public function __construct(int $maxSize = 4096)
    {
        $this->hardMaxSize = $maxSize;
    }
    /**
     * Sets the upper limit on table size. Dynamic table updates requesting a size above this size will result in a
     * decoding error (i.e., returning null from decode()).
     *
     * @param int $maxSize
     */
    public function setTableSizeLimit(int $maxSize)
    {
        $this->hardMaxSize = $maxSize;
    }
    /**
     * Resizes the table to the given size, removing old entries as per section 4.4 if necessary.
     *
     * @param int|null $size
     */
    public function resizeTable(int $size = NULL)
    {
        if ($size !== null) {
            $this->currentMaxSize = \max(0, \min($size, $this->hardMaxSize));
        }
        while ($this->size > $this->currentMaxSize) {
            [$name, $value] = \array_pop($this->headers);
            $this->size -= 32 + \strlen($name) + \strlen($value);
        }
    }
    /**
     * @param string $input Encoded headers.
     * @param int $maxSize Maximum length of the decoded header string.
     *
     * @return string[][]|null Returns null if decoding fails or if $maxSize is exceeded.
     */
    public function decode(string $input, int $maxSize)
    {
        $headers = [];
        $off = 0;
        $inputLength = \strlen($input);
        $size = 0;
        try {
            // dynamic $table as per 2.3.2
            while ($off < $inputLength) {
                $index = \ord($input[$off++]);
                if ($index & 0x80) {
                    // range check
                    if ($index <= self::LAST_INDEX + 0x80) {
                        if ($index === 0x80) {
                            return null;
                        }
                        [$name, $value] = $headers[] = self::TABLE[$index - 0x81];
                    } else {
                        if ($index == 0xff) {
                            $index = self::decodeDynamicInteger($input, $off) + 0xff;
                        }
                        $index -= 0x81 + self::LAST_INDEX;
                        if (!isset($this->headers[$index])) {
                            return null;
                        }
                        [$name, $value] = $headers[] = $this->headers[$index];
                    }
                } elseif (($index & 0x60) !== 0x20) {
                    // (($index & 0x40) || !($index & 0x20)): bit 4: never index is ignored
                    $dynamic = (bool) ($index & 0x40);
                    if ($index & ($dynamic ? 0x3f : 0xf)) {
                        // separate length
                        if ($dynamic) {
                            if ($index === 0x7f) {
                                $index = self::decodeDynamicInteger($input, $off) + 0x3f;
                            } else {
                                $index &= 0x3f;
                            }
                        } else {
                            $index &= 0xf;
                            if ($index === 0xf) {
                                $index = self::decodeDynamicInteger($input, $off) + 0xf;
                            }
                        }
                        if ($index < 0) {
                            return null;
                        }
                        if ($index <= self::LAST_INDEX) {
                            $header = self::TABLE[$index - 1];
                        } elseif (!isset($this->headers[$index - 1 - self::LAST_INDEX])) {
                            return null;
                        } else {
                            $header = $this->headers[$index - 1 - self::LAST_INDEX];
                        }
                    } else {
                        if ($off >= $inputLength) {
                            return null;
                        }
                        $length = \ord($input[$off++]);
                        $huffman = $length & 0x80;
                        $length &= 0x7f;
                        if ($length === 0x7f) {
                            $length = self::decodeDynamicInteger($input, $off) + 0x7f;
                        }
                        if ($inputLength - $off < $length || $length <= 0) {
                            return null;
                        }
                        if ($huffman) {
                            $header = [self::huffmanDecode(\substr($input, $off, $length))];
                            if ($header[0] === null) {
                                return null;
                            }
                        } else {
                            $header = [\substr($input, $off, $length)];
                        }
                        $off += $length;
                    }
                    if ($off >= $inputLength) {
                        return null;
                    }
                    $length = \ord($input[$off++]);
                    $huffman = $length & 0x80;
                    $length &= 0x7f;
                    if ($length === 0x7f) {
                        $length = self::decodeDynamicInteger($input, $off) + 0x7f;
                    }
                    if ($inputLength - $off < $length || $length < 0) {
                        return null;
                    }
                    if ($huffman) {
                        $header[1] = self::huffmanDecode(\substr($input, $off, $length));
                        if ($header[1] === null) {
                            return null;
                        }
                    } else {
                        $header[1] = \substr($input, $off, $length);
                    }
                    $off += $length;
                    if ($dynamic) {
                        \array_unshift($this->headers, $header);
                        $this->size += 32 + \strlen($header[0]) + \strlen($header[1]);
                        if ($this->currentMaxSize < $this->size) {
                            $this->resizeTable();
                        }
                    }
                    [$name, $value] = $headers[] = $header;
                } else {
                    // if ($index & 0x20) {
                    if ($off >= $inputLength) {
                        return null;
                        // Dynamic table size update must not be the last entry in header block.
                    }
                    $index &= 0x1f;
                    if ($index === 0x1f) {
                        $index = self::decodeDynamicInteger($input, $off) + 0x1f;
                    }
                    if ($index > $this->hardMaxSize) {
                        return null;
                    }
                    $this->resizeTable($index);
                    continue;
                }
                $size += \strlen($name) + \strlen($value);
                if ($size > $maxSize) {
                    return null;
                }
            }
        } catch (HPackException $e) {
            return null;
        }
        return $headers;
    }
    private static function encodeDynamicInteger(int $int) : string
    {
        $out = "";
        for ($i = 0; $int >> $i > 0x80; $i += 7) {
            $out .= \chr(0x80 | $int >> $i & 0x7f);
        }
        return $out . \chr($int >> $i);
    }
    /**
     * @param string[][] $headers
     * @param int $compressionThreshold Compress strings whose length is at least the number of bytes given.
     *
     * @return string
     */
    public function encode(array $headers, int $compressionThreshold = 1024) : string
    {
        // @TODO implementation is deliberately primitive... [doesn't use any dynamic table...]
        $output = "";
        foreach ($headers as [$name, $value]) {
            if (isset(self::$indexMap[$name])) {
                $index = self::$indexMap[$name];
                if ($index < 0x10) {
                    $output .= \chr($index);
                } else {
                    $output .= "\x0f" . \chr($index - 0xf);
                }
            } else {
                $output .= "\x00" . $this->encodeString($name, $compressionThreshold);
            }
            $output .= $this->encodeString($value, $compressionThreshold);
        }
        return $output;
    }
    private function encodeString(string $value, int $compressionThreshold) : string
    {
        $prefix = "\x00";
        if (\strlen($value) >= $compressionThreshold) {
            $value = self::huffmanEncode($value);
            $prefix = "\x80";
        }
        if (\strlen($value) < 0x7f) {
            return ($prefix | \chr(\strlen($value))) . $value;
        }
        return ($prefix | "") . self::encodeDynamicInteger(\strlen($value) - 0x7f) . $value;
    }
}
(function () {
    static::init();
})->bindTo(null, HPackNative::class)();