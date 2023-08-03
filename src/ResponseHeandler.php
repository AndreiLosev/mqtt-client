<?php

namespace Losev\MqttClient;

use Losev\MqttClient\Contracts\NetTransport;

class ResponseHeandler
{
    /** 
     * @param NetTransport $pipe 
     */
    public function __construct(
        private NetTransport $pipe,
    ) {}

    public function getPackageType(): PackageType
    {
        $byte = $this->pipe->read(1);

        return PackageType::fromFirestByte($byte);
    }

    public function getPackageSize(): int
    {
        /** @var int */
        $result = 0;
        $factor = 1;

        while (true) { 
            $byte = $this->pipe->read(1);
            $encodedByte = ord($byte); 
            $result += ($encodedByte & 0x7f) * $factor;
            $factor <<= 7;
            if (!($encodedByte & 0x80)) {
                return $result;
            }
            if ($factor > 0x200000) {
                throw new \RuntimeException("Invalid Remaining bytes");
            }
        }
    }
}
