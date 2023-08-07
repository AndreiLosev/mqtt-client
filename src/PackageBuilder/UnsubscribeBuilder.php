<?php

namespace Losev\MqttClient\PackageBuilder;

use function Losev\MqttClient\remainingBytes;
use function Losev\MqttClient\strEndcode;

class UnsubscribeBuilder
{
    /** 
     * @param string[] $themes 
     */
    public function build(int $packageId, array $themes): string
    {
        $fixedHeaderFirstByte = "\xa2";
        $remainingBytesValue = pack('n', $packageId) . self::pyload($themes);
        $fixedHeaderSecondByte = remainingBytes($remainingBytesValue);

        return $fixedHeaderFirstByte . $fixedHeaderSecondByte . $remainingBytesValue;
    }

    /** 
     * @param string[] $themes 
     */
    public static function pyload(array $themes): string
    {
        $result = '';

        for ($i = 0; $i  < count($themes); $i++) { 
            $result .= strEndcode($themes[$i]); 
        }

        return $result;
    }
}
