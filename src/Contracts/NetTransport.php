<?php

namespace Losev\MqttClient\Contracts;

interface NetTransport
{
    public function read(int $len): string;

    public function write(string $data): int;
}
