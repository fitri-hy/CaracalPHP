<?php
namespace Caracal\Core;

final class CUID
{
    private const VERSION = '1.1.0';
    private const EPOCH   = 1700000000000000;

    private const BASE62 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    private static int $lastTime = 0;
    private static int $sequence = 0;

    private static int $datacenter = 0;
    private static int $worker     = 0;

    public static function configure(int $datacenter, int $worker): void
    {
        self::$datacenter = $datacenter & 0xFF;
        self::$worker     = $worker & 0xFF;
    }

    public static function node(): array
    {
        return [
            'datacenter' => self::$datacenter,
            'worker'     => self::$worker,
        ];
    }

    public static function version(): string
    {
        return self::VERSION;
    }

    public static function id(): string
    {
        return self::encode(self::binary());
    }

    public static function binary(): string
    {
        return self::generate();
    }

    public static function fromBinary(string $binary): string
    {
        return self::encode($binary);
    }

    private static function generate(): string
    {
        $now = (int)(microtime(true) * 1_000_000);

        if ($now < self::$lastTime) {
            $now = self::$lastTime;
        }

        if ($now === self::$lastTime) {
            self::$sequence++;

            if (self::$sequence > 0xFFFF) {
                do {
                    $now = (int)(microtime(true) * 1_000_000);
                } while ($now <= self::$lastTime);

                self::$sequence = 0;
            }
        } else {
            self::$sequence = 0;
        }

        self::$lastTime = $now;

        $relative = $now - self::EPOCH;

        return
            self::packUint64($relative) .
            pack('C', self::$datacenter) .
            pack('C', self::$worker) .
            pack('n', self::$sequence) .
            random_bytes(4);
    }

    private static function packUint64(int $value): string
    {
        $hi = ($value & 0xFFFFFFFF00000000) >> 32;
        $lo = $value & 0xFFFFFFFF;

        return pack('NN', $hi, $lo);
    }

    private static function unpackUint64(string $binary): int
    {
        $parts = unpack('Nhi/Nlo', $binary);
        return ($parts['hi'] << 32) | $parts['lo'];
    }

    public static function decode(string $binary): array
    {
        $timestamp = self::unpackUint64(substr($binary, 0, 8));

        $dc  = ord($binary[8]);
        $wk  = ord($binary[9]);
        $seq = unpack('n', substr($binary, 10, 2))[1];
        $ent = bin2hex(substr($binary, 12, 4));

        return [
            'timestamp_micro' => $timestamp + self::EPOCH,
            'datacenter'      => $dc,
            'worker'          => $wk,
            'sequence'        => $seq,
            'entropy'         => $ent,
            'version'         => self::VERSION,
        ];
    }

    public static function decodeId(string $id): array
    {
        return self::decode(self::decodeBase62($id));
    }

    public static function timestampFromId(string $id): int
    {
        $binary = self::decodeBase62($id);

        $timestamp = self::unpackUint64(substr($binary, 0, 8));

        return $timestamp + self::EPOCH;
    }

    public static function datetime(string $binary): string
    {
        $ts = self::decode($binary)['timestamp_micro'];

        $sec = intdiv($ts, 1_000_000);
        $micro = $ts % 1_000_000;

        return date('Y-m-d H:i:s', $sec) . '.' . str_pad($micro, 6, '0', STR_PAD_LEFT);
    }

    public static function uuid(string $binary): string
    {
        $hex = bin2hex($binary);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    public static function isValid(string $id): bool
    {
        return preg_match('/^[0-9A-Za-z]{10,30}$/', $id) === 1;
    }

    public static function shard(string $id, int $mod = 16): int
    {
        return abs(crc32($id)) % $mod;
    }

    public static function benchmark(int $loops = 10000): string
    {
        $start = microtime(true);

        for ($i = 0; $i < $loops; $i++) {
            self::id();
        }

        $end = microtime(true);

        return number_format(($end - $start) * 1000, 4) . " ms ({$loops} IDs)";
    }

    private static function encode(string $data): string
    {
        if (extension_loaded('gmp')) {
            $num = gmp_import($data);
            return gmp_strval($num, 62);
        }

        return self::base62Encode($data);
    }

    private static function base62Encode(string $data): string
    {
        $num = gmp_import($data);

        $chars = self::BASE62;
        $base = strlen($chars);

        $out = '';

        while (gmp_cmp($num, 0) > 0) {
            $rem = gmp_intval(gmp_mod($num, $base));
            $out = $chars[$rem] . $out;
            $num = gmp_div_q($num, $base);
        }

        return $out ?: '0';
    }

    private static function decodeBase62(string $id): string
    {
        if (extension_loaded('gmp')) {
            $num = gmp_init($id, 62);
            $bin = gmp_export($num);
            return str_pad($bin, 16, "\x00", STR_PAD_LEFT);
        }

        $chars = self::BASE62;
        $map = array_flip(str_split($chars));

        $num = gmp_init(0);

        foreach (str_split($id) as $char) {
            $num = gmp_add(
                gmp_mul($num, 62),
                $map[$char]
            );
        }

        $bin = gmp_export($num);

        return str_pad($bin, 16, "\x00", STR_PAD_LEFT);
    }
}