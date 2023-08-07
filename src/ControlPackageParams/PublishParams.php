<?php

namespace Losev\MqttClient\ControlPackageParams;

class PublishParams
{
    public function __construct(
        public int $QoS = 0,
        public bool $isRetain = false,
        public bool $isDup = false,
        public string $theme,
        public string $payload,
    ) {}
}
