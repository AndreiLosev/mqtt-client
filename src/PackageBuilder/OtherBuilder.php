<?php

namespace Losev\MqttClient\PackageBuilder;

class OtherBuilder
{
    public function disconnectPackage(): string
    {
        return "\xe0\x00";
    }

    public function pingreqPackage(): string
    {
        return "\xc0\x00";
    }

    public function pubackPackage(int $packageId): string
    {
        $fixedHeaderFirstByte = "\x40";
        $remainingBytesValue = pack('n', $packageId);
        $fixedHeaderSecondByte = "\x02";
        return $fixedHeaderFirstByte . $fixedHeaderSecondByte . $remainingBytesValue;
    }

    public function pubrecPackage(int $packageId): string
    {
        $fixedHeaderFirstByte = "\x50";
        $remainingBytesValue = pack('n', $packageId);
        $fixedHeaderSecondByte = "\x02";
        return $fixedHeaderFirstByte . $fixedHeaderSecondByte . $remainingBytesValue;
    }

    public function pubcompPackage(int $packageId): string
    {
        $fixedHeaderFirstByte = "\x70";
        $remainingBytesValue = pack('n', $packageId);
        $fixedHeaderSecondByte = "\x02";
        return $fixedHeaderFirstByte . $fixedHeaderSecondByte . $remainingBytesValue;
    }

    public function pubrelPackage(int $packageId): string
    {
        $fixedHeaderFirstByte = "\x62";
        $remainingBytesValue = pack('n', $packageId);
        $fixedHeaderSecondByte = "\x02";
        return $fixedHeaderFirstByte . $fixedHeaderSecondByte . $remainingBytesValue;
    }
}
