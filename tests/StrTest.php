<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use function Losev\MqttClient\getLen;
use function Losev\MqttClient\remainingBytes;
use function Losev\MqttClient\strDecode;
use function Losev\MqttClient\strEndcode;

class StrTest extends TestCase
{
    public function testMessageToMqttMessage(): void
    {
        $value = "A\xF0\xAA\x9B\x94";
        $result = strEndcode($value);
        $expect = "\x00\x05\x41\xF0\xAA\x9B\x94";

        $this->assertSame($expect, $result);
    }

    public function testMqttMessageToMessages(): void
    {
        $value = "\x00\x04Wasa\x00\x08Вася";
        $result = strDecode($value);
        $expect = ['Wasa', 'Вася'];

        $this->assertSame($expect, $result);
    }

    public function testRemainingBytes(): void
    {
        $values = [
            'Hello world',
            "Hello world! - Hello world! - Hello world! - Hello world! - Hello world!
            - Hello world! - Hello world! - Hello world! - Hello world! - Hello world!
            - Hello world! - Hello world! - Hello world! - Hello world! - Hello world!",
            str_repeat('01234567', 4*512),
        ];

        $expect = ["\x0b", "\xf6\x01", "\x80\x80\x01"];

        $result = array_map(fn($v) => remainingBytes($v), $values);

        $this->assertSame($expect, $result);
    }

    public function testGetLength(): void
    {
        $expect = [
            'Hello world',
            "Hello world! - Hello world! - Hello world! - Hello world! - Hello world!
            - Hello world! - Hello world! - Hello world! - Hello world! - Hello world!
            - Hello world! - Hello world! - Hello world! - Hello world! - Hello world!",
            str_repeat('01234567', 4*512),
        ];

        $value1 = "\x0b" . 'Hello world';
        $value2 = "\xf6\x01" . "Hello world! - Hello world! - Hello world! - Hello world! - Hello world!
            - Hello world! - Hello world! - Hello world! - Hello world! - Hello world!
            - Hello world! - Hello world! - Hello world! - Hello world! - Hello world!";
        $value3 = "\x80\x80\x01" . str_repeat('01234567', 4*512);

        $res1 = getLen($value1);
        $res2 = getLen($value2);
        $res3 = getLen($value3);
        
        $this->assertSame([11, 246, 16384], [$res1, $res2, $res3]);
        $this->assertSame($expect, [$value1, $value2, $value3]);
    }
}
