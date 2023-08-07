<?php

namespace Losev\MqttClient\ControlPackageParams;

class SubscribeParams
{
    public function __construct(
        public string $theme,
        public int $QoS,
    ) {}
}
