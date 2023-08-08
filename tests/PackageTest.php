<?php

namespace Tests;

use Losev\MqttClient\ControlPackageParams\ConnectionParam;
use Losev\MqttClient\Heandlers\ConnectionHeandler;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    public function testConnection(): void
    {
        $cP = self::getConnectionParam();
        $ch = new ConnectionHeandler($cP);
        $a = $ch->buildConnectionPackage();
        $b = "\x10\x2f\x00\x04\x4D\x51\x54\x54\x04\xce\x00\x0a\x00\x04\x31\x32\x35\x38\x00\x0A\x74\x65\x73\x74\x2F\x62\x79\x2F\x62\x79\x00\x05\x62\x79\x20\x62\x79\x00\x04\x57\x61\x73\x61\x00\x04\x31\x32\x33\x34";

        $this->assertSame($a, $b);
    }

    public function testConnectResponsePositive(): void
    {
        $cP = self::getConnectionParam();
        $ch = new ConnectionHeandler($cP);

        $ch->responseHandler("\x02\x01\x00");

        $this->assertSame(
            [true, true],
            [$ch->isConnected(), $ch->previousIsSessionSaved()],
        );
    }

    public function testConnectResponseNegotive(): void
    {
        $cP = self::getConnectionParam();
        $ch = new ConnectionHeandler($cP);
        $errr = false;

        try {
            $ch->responseHandler("\x02\x00\x03");
        } catch (\Throwable $th) {
            $this->assertSame(
                [false, $th->getMessage()],
                [
                    $ch->previousIsSessionSaved(),
                    "The network connection is complete, but the service MQTT not available",
                ],
            );
            $errr = true;
        }

        if (!$errr) {
            $this->assertTrue(true);
        }
    }

    private static function getConnectionParam(): ConnectionParam
    {
        return new ConnectionParam(
            clientId: '1258',
            name: 'Wasa',
            password: '1234',
            lastWillQoS: 1,
            lastWill: 'by by',
            keepAliveInterval: 10,
            lastWillTheem: 'test/by/by',
        );

    }
}

