<?php

namespace Losev\MqttClient\PackageBuilder;

use Losev\MqttClient\ControlPackageParams\SubscribeParams;
use function Losev\MqttClient\remainingBytes;
use function Losev\MqttClient\strEndcode;

class SubscribeBuilder
{
    /** 
     * @param SubscribeParams[] $payload 
     */
    public function subscribePackage(int $packageId, array $payload): string
    {
        $fixedHeaderFirstByte = "\x82";
        $remainingBytesValue = pack('n', $packageId) . self::payload($payload);
        $fixedHeaderSecondByte = remainingBytes($remainingBytesValue);

        return $fixedHeaderFirstByte . $fixedHeaderSecondByte . $remainingBytesValue;
    }

    /** 
     * @param SubscribeParams[] $payload 
     */
    private static function payload(array $payload): string
    {
        $result = '';

        for ($i = 0; $i  < count($payload); $i++) { 
            $message = strEndcode($payload[$i]->theme) . chr($payload[$i]->QoS);
            $result .= $message;
        }

        return $result;
    }
}
