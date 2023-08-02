<?php

namespace Losev\MqttClient;

class ConnectionParam
{
    const V3_1_1 = "\x04";

    public function __construct(
        public string $clientId,
        public null|string $name = null,
        public null|string $password = null,
        public bool $saveLastWill = false,
        public int $lastWillQoS = 0,
        public null|string $lastWill = null,
        public null|string $lastWillTheem = null,
        public bool $cleaningFlag = true,
        public int $pingPeriodSec = 300,
        public string $version = self::V3_1_1,
    )
    {
        if (is_null($lastWill) && $lastWillQoS > 0) {
            throw new \RuntimeException("if lastWill is empty lastWillQoS must be 0");
        }
        if (is_null($lastWill) && $saveLastWill) {
            throw new \RuntimeException("if lastWill is empty saveLastWill must be false");
        }

        if (is_null($name) && !is_null($password)) {
            throw new \RuntimeException("if name is null. password must be null");
        }

        if ($clientId === '' && !$cleaningFlag) {
            throw new \RuntimeException('if $clientId is empty-string. cleaningFlag must be true');
        }

        if (!is_null($lastWill) && is_null($lastWillTheem)) {
            throw new \RuntimeException('if lastWill is exists, lastWillTheem must be string');
        }
    }
}
