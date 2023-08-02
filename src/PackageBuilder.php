<?php

namespace Losev\MqttClient;

class PackageBuilder
{
    public function connectionPackage(ConnectionParam $cp): string
    {
        $fixedHeaderFirstByte = "\x10";
        $remainingBytesValue = self::connectVaribleHeader($cp)
            . self::connectionPyload($cp);
        $fixedHeaderSecondByte = remainingBytes($remainingBytesValue);

        return $fixedHeaderFirstByte . $fixedHeaderSecondByte . $remainingBytesValue;
    }

    private static function connectVaribleHeader(ConnectionParam $cp): string
    {
        $connectFlags = [
            is_null($cp->name) ? 0 : 0x80,
            is_null($cp->password) ? 0 : 0x40,
            (int)$cp->saveLastWill * 0x20,
            $cp->lastWillQoS << 3,
            is_null($cp->lastWill) ? 0 : 0x04,
            (int)$cp->cleaningFlag * 0x02,
        ];

        $result = [
            "\x00\x04MQTT",
            $cp->version,
            chr(array_sum($connectFlags)),
            pack('n', $cp->pingPeriodSec),
        ];
        
        return implode('', $result);
    }

    private static function connectionPyload(ConnectionParam $cp): string
    {
        $result = [
            strEndcode($cp->clientId),
            is_null($cp->lastWill) || is_null($cp->lastWillTheem)
                ? '' : strEndcode($cp->lastWillTheem),
            is_null($cp->lastWill) ? '' : strEndcode($cp->lastWill),
            is_null($cp->name) ? '' : strEndcode($cp->name),
            is_null($cp->password) ? '' : strEndcode($cp->password),
        ];

        return implode('', $result);
    }
}
