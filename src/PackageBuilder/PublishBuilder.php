<?php

namespace Losev\MqttClient\PackageBuilder;

use Losev\MqttClient\ControlPackageParams\PublishParams;
use function Losev\MqttClient\remainingBytes;
use function Losev\MqttClient\strEndcode;

class PublishBuilder
{
    public function build(int $packageId, PublishParams $pb): string
    {
        $fixedHeaderFirstByte = self::fixedHeaderFirstByte($pb);
        $remainingBytesValue = self::varibleHeader($packageId, $pb) . $pb->payload;
        $fixedHeaderSecondByte = remainingBytes($remainingBytesValue);

        return $fixedHeaderFirstByte . $fixedHeaderSecondByte . $remainingBytesValue;
    }

    public static function fixedHeaderFirstByte(PublishParams $pb): string
    {
        $ifixedHeaderFirstByte = 0x30 + (int)$pb->isRetain
            + ($pb->QoS << 1) + 8 * (int)$pb->isDup;

        return chr($ifixedHeaderFirstByte);
    }

    public static function varibleHeader(int $packageId, PublishParams $pb): string
    {
        return strEndcode($pb->theme) . pack('n', $packageId);
    }
}
