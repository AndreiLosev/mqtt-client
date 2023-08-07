<?php

namespace Tests;

use Losev\MqttClient\ControlPackageParams\ConnectionParam;
use Losev\MqttClient\Heandlers\ConnectionHeandler;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    public function testConnection(): void
    {
        $cP = new ConnectionParam(
            clientId: '1258',
            name: 'Wasa',
            password: '1234',
            lastWillQoS: 1,
            lastWill: 'by by',
            keepAliveInterval: 10,
            lastWillTheem: 'test/by/by',
        );

        $ch = new ConnectionHeandler($cP);
        $a = $ch->buildConnectionPackage();
        $b = "\x10\x2f\x00\x04\x4D\x51\x54\x54\x04\xce\x00\x0a\x00\x04\x31\x32\x35\x38\x00\x0A\x74\x65\x73\x74\x2F\x62\x79\x2F\x62\x79\x00\x05\x62\x79\x20\x62\x79\x00\x04\x57\x61\x73\x61\x00\x04\x31\x32\x33\x34";

        $this->assertSame($a, $b);
    }
}
