<?php

namespace Losev\MqttClient;

use Losev\MqttClient\PackageBuilder\ConnectionBuilder;
use Losev\MqttClient\PackageBuilder\SubscribeBuilder;

class PackageBuilder
{
    public function __construct(
        public readonly ConnectionBuilder $connect = new ConnectionBuilder(),
        public readonly SubscribeBuilder $subscribe = new SubscribeBuilder(),
    ) {}
}
