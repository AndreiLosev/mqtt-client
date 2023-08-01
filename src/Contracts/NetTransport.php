<?php

namespace Losev\MqttClient\Contracts;

interface NetTransport
{
    public function read(int $len): string|false;

    public function write(string $data): int|false;
}
