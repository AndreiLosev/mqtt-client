<?php

namespace Losev\MqttClient\Storages;

class InMemoryStorage implements Storage
{
    private int $counter = 0;

    /** 
     * @var array<int, PendingResponse> 
     */
    private array $store = [];

    public function addPendingResponse(PendingResponse $pR): int
    {
        if (is_null($pR->id)) {
            $this->counter += 1;
            $pR->id = $this->counter;
        }

        if (is_null($pR->package)) {
            unset($this->store[$pR->id]);
        } else {
            $this->store[$this->counter] = $pR;
        }

        return $this->counter;
    }
}
