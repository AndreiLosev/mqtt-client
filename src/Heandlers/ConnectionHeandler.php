<?php

namespace Losev\MqttClient\Heandlers;

use Losev\MqttClient\ControlPackageParams\ConnectionParam;
use function Amp\delay;
use function Amp\now;
use function Losev\MqttClient\remainingBytes;
use function Losev\MqttClient\strEndcode;

class ConnectionHeandler
{
    private bool $connected = false;

    private bool $previousSessionSaved = false;

    private float $lastPackageTimestemp = 0;

    public function __construct(
        private ConnectionParam $cp,
    ) {}

    public function buildConnectionPackage(): string
    {
        $fixedHeaderFirstByte = "\x10";
        $remainingBytesValue = $this->varibleHeader() . $this->pyload();
        $fixedHeaderSecondByte = remainingBytes($remainingBytesValue);

        return $fixedHeaderFirstByte . $fixedHeaderSecondByte . $remainingBytesValue;
    }

    /** 
     * @return 3 
     */
    public function responseLength(): int
    {
        return 3;
    }

    public function responseHandler(string $bytes): void
    {
        $result = unpack('C*', $bytes, 1);

        $this->previousSessionSaved = (bool)$result[1];

        $this->connected = match ($result[2]) {
            5 => throw new \RuntimeException(
                "Server does not support protocol level MQTT requested by Client",
                $result[2]
            ),
            4 => throw new \RuntimeException(
                "The Client ID is a well-formed UTF-8 string, but not allowed by the Server",
                $result[2]
            ),
            3 => throw new \RuntimeException(
                "The network connection is complete, but the service MQTT not available",
                $result[2]
            ),
            2 => throw new \RuntimeException(
                "The data in the username or password is not true",
                $result[2]
            ),
            1 => throw new \RuntimeException(
                "The client is not allowed to connect",
                $result[2]
            ),
            0 => true,
            default => throw new \RuntimeException("unknown exception"),
            
        };

        $this->lastPackageTimestemp = now();
    }

    public function resetPingTimer(): void
    {
        $this->lastPackageTimestemp = now(); 
    }

    public function disconnectPackage(): string
    {
        return "\xe0\x00";
    }

    public function isConnected(): bool
    {
        return $this->connected;
    }

    public function previousIsSessionSaved(): bool
    {
        return $this->previousSessionSaved;
    } 

    /** 
     * @param callable(string): void $sendPing
     */
    public function ping(callable $sendPing): void
    {
        try {
            while (true) {
                delay($this->cp->keepAliveInterval / 10);
                if ($this->elapsed() <= $this->cp->keepAliveInterval) {
                    continue;
                }

                $sendPing("\xc0\x00");
                $this->resetPingTimer();
            }
        } finally {
            $this->connected = false;
        }
    }

    private function elapsed(): float
    {
        return now() - $this->lastPackageTimestemp;
    }

    private function varibleHeader(): string
    {
        $connectFlags = [
            is_null($this->cp->name) ? 0 : 0x80,
            is_null($this->cp->password) ? 0 : 0x40,
            (int)$this->cp->saveLastWill * 0x20,
            $this->cp->lastWillQoS << 3,
            is_null($this->cp->lastWill) ? 0 : 0x04,
            (int)$this->cp->cleaningFlag * 0x02,
        ];

        return "\x00\x04MQTT"
            . $this->cp->version
            . chr(array_sum($connectFlags))
            . pack('n', $this->cp->keepAliveInterval);
    }

    private function pyload(): string
    {
        $lastWillTheem = is_null($this->cp->lastWill)
            || is_null($this->cp->lastWillTheem)
                ? '' : strEndcode($this->cp->lastWillTheem);

        return strEndcode($this->cp->clientId)
            . $lastWillTheem
            . (is_null($this->cp->lastWill) ? '' : strEndcode($this->cp->lastWill))
            . (is_null($this->cp->name) ? '' : strEndcode($this->cp->name))
            . (is_null($this->cp->password) ? '' : strEndcode($this->cp->password));
    }
}
