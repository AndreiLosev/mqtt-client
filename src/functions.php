<?php

namespace Losev\MqttClient;

function strEndcode(string $message): string
{
    $len = strlen($message);

    if ($len > 0xffff) {
        throw new \RuntimeException("too big message, max: 0хffff bytes");
    }

    $arrLen = [$len >> 8, $len % 0x0100];
    return pack('C*', ...$arrLen) . $message;
}

/** 
 * @return string[] 
 */
function strDecode(string $bytes): array
{
    $result = [];

    while ($bytes !== '') {
        /** @var int */
        $len = current(unpack('n', substr($bytes, 0, 2)));
        $result[] = substr($bytes, 2, $len);
        $bytes = substr($bytes, 2 + $len);
    }

    return $result;
}

function remainingBytes(string $bytes): string
{
    $result = [];
    $length = strlen($bytes);

    do {
        $encodedByte = $length % 0x80;
        $length >>= 7;

        if ($length > 0) {
            $encodedByte |= 0x80;
        }

        $result[] = $encodedByte;
    } while ($length > 0);

    /** @var string */
    return pack('C*', ...$result);
}

function getLen(string &$bytes): int
{
    /** @var int */
    $result = 0;
    $factor = 1;
    while (true) { 
        $encodedByte = ord($bytes[0]); 
        $result += ($encodedByte & 0x7f) * $factor;
        $factor <<= 7;
        $bytes = substr($bytes, 1);
        if (!($encodedByte & 0x80)) {
            return $result;
        }
        if ($factor > 0x200000) {
            throw new \RuntimeException("Invalid Remaining bytes");
        }
    }
}

/** 
 * @param callable(int): string $read
 */
function getPackageSize(callable $read): int
{
    /** @var int */
    $result = 0;
    $factor = 1;

    while (true) { 
        $encodedByte = ord($read(1)); 
        $result += ($encodedByte & 0x7f) * $factor;
        $factor <<= 7;
        if (!($encodedByte & 0x80)) {
            return $result;
        }
        if ($factor > 0x200000) {
            throw new \RuntimeException("Invalid Remaining bytes");
        }
    }
}

/** 
 * @param string[] $messages 
 */
function dbg(...$messages): void
{
    $result = [];

    /** @var string $m */
    foreach ($messages as $m) {
        $arr = unpack('C*', $m);
        if (!$arr) {
            continue;
        }
        /** @var int $v */
        foreach ($arr as $key => $v) {
            $result[$key][] = sprintf('\\x%02X', $v);
        }
    }

    foreach ($result as $value) {
        if (count($value) === 1) {
            echo $value[0] . ' ';
        } else {
            echo implode(" - ", $value) . PHP_EOL;
        }
    }
}
