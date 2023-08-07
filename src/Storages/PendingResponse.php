<?php

namespace Losev\MqttClient\Storages;

use Losev\MqttClient\PackageType;

class PendingResponse
{
    public int $time;

    public function __construct(
        public null|string $package,
        public null|int $id = null,
    ) {
        $this->time = time();
    }
}
