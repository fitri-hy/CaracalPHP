<?php

namespace Caracal\Core;

final class CUID
{
    private const VERSION = '1.0.0';
    private const EPOCH   = 1700000000000000;

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
            'worker'     => self::$worker
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
        $now = (int)(microtime(true) * 1000000);

        if ($now < self::$lastTime) {
            $now = self::$lastTime;
        }

        if ($now === self::$lastTime) {
            self::$sequence++;
            if (self::$sequence > 0xFFFF) {
                do {
                    $now = (int)(microtime(true) * 1000000);
                } while ($now <= self::$lastTime);
                self::$sequence = 0;
            }
        } else {
            self::$sequence = 0;
        }

        self::$lastTime = $now;

        $relative = $now - self::EPOCH;

        $high = ($relative >> 32) & 0xFFFFFFFF;
        $low  = $relative & 0xFFFFFFFF;

        return
            pack('N2', $high, $low) .
            pack('C', self::$datacenter) .
            pack('C', self::$worker) .
            pack('n', self::$sequence) .
            random_bytes(4);
    }

    public static function decode(string $binary): array
    {
        $parts = unpack('Nhigh/Nlow', substr($binary, 0, 8));
        $time  = ($parts['high'] << 32) | $parts['low'];

        $dc  = unpack('Cdc', substr($binary, 8, 1))['dc'];
        $wk  = unpack('Cwk', substr($binary, 9, 1))['wk'];
        $seq = unpack('nseq', substr($binary, 10, 2))['seq'];
        $ent = bin2hex(substr($binary, 12, 4));

        return [
            'timestamp_micro' => $time + self::EPOCH,
            'datacenter'      => $dc,
            'worker'          => $wk,
            'sequence'        => $seq,
            'entropy'         => $ent,
            'version'         => self::VERSION
        ];
    }

    public static function decodeId(string $id): array
    {
        return self::decode(self::decodeBase62($id));
    }

    public static function timestampFromId(string $id): int
    {
        $binary = self::decodeBase62($id);
        $parts  = unpack('Nhigh/Nlow', substr($binary, 0, 8));
        $time   = ($parts['high'] << 32) | $parts['low'];

        return $time + self::EPOCH;
    }

    public static function datetime(string $binary): string
    {
        $ts = self::decode($binary)['timestamp_micro'];
        $sec = intdiv($ts, 1000000);
        $micro = $ts % 1000000;

        return date('Y-m-d H:i:s', $sec)
            . '.' . str_pad($micro, 6, '0', STR_PAD_LEFT);
    }

    public static function uuid(string $binary): string
    {
        $hex = bin2hex($binary);

        return sprintf('%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    public static function isValid(string $id): bool
    {
        return preg_match('/^[A-Za-z0-9]+$/', $id) === 1;
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

        return number_format(($end - $start) * 1000, 4)
            . " ms ({$loops} IDs)";
    }

    private static function encode(string $data): string
    {
        if (extension_loaded('gmp')) {
            $num = gmp_import($data);
            return gmp_strval($num, 62);
        }

        $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $bytes = array_values(unpack('C*', $data));
        $result = '';

        while (!empty($bytes)) {
            $carry = 0;
            $new = [];

            foreach ($bytes as $byte) {
                $value = ($carry << 8) + $byte;
                $quot  = intdiv($value, 62);
                $carry = $value % 62;

                if (!empty($new) || $quot > 0) {
                    $new[] = $quot;
                }
            }

            $result = $alphabet[$carry] . $result;
            $bytes  = $new;
        }

        return $result ?: '0';
    }

    private static function decodeBase62(string $id): string
    {
        $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        if (extension_loaded('gmp')) {
            $num = gmp_init($id, 62);
            return gmp_export($num);
        }

        if (!extension_loaded('bcmath')) {
            throw new \RuntimeException('GMP or BCMath extension required.');
        }

        $num = '0';

        for ($i = 0; $i < strlen($id); $i++) {
            $num = bcmul($num, '62');
            $num = bcadd($num, (string) strpos($alphabet, $id[$i]));
        }

        $binary = '';

        while (bccomp($num, '0') > 0) {
            $byte = bcmod($num, '256');
            $binary = chr((int)$byte) . $binary;
            $num = bcdiv($num, '256', 0);
        }

        return str_pad($binary, 16, "\x00", STR_PAD_LEFT);
    }
}