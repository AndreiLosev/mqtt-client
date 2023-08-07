<?php

namespace Losev\MqttClient\Storages;

interface Storage
{
    public function addPendingResponse(PendingResponse $pR): int;
}
