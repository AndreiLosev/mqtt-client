<?php

namespace Losev\MqttClient\ControlPackageParams;

class SubscribeParams
{
    public function __construct(
        public string $theem,
        public int $QoS,
    ) {}
}
