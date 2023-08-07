<?php

namespace Losev\MqttClient;

use Losev\MqttClient\PackageBuilder\ConnectionBuilder;
use Losev\MqttClient\PackageBuilder\OtherBuilder;
use Losev\MqttClient\PackageBuilder\PublishBuilder;
use Losev\MqttClient\PackageBuilder\SubscribeBuilder;
use Losev\MqttClient\PackageBuilder\UnsubscribeBuilder;

class PackageBuilder
{
    public function __construct(
        public readonly ConnectionBuilder $connect = new ConnectionBuilder(),
        public readonly SubscribeBuilder $subscribe = new SubscribeBuilder(),
        public readonly PublishBuilder $publish = new PublishBuilder(),
        public readonly UnsubscribeBuilder $unsubscribe = new UnsubscribeBuilder(),
        public readonly OtherBuilder $otherBuilder = new OtherBuilder(),
    ) {}
}
