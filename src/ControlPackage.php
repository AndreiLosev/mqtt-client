<?php

namespace Losev\MqttClient;

use Losev\MqttClient\ControlPackageParams\PublishParams;

enum ControlPackage
{
    case CONNECT;
    case CONNACK;
    case PUBLISH;
    case PUBACK;
    case PUBREC;
    case PUBREL;
    case PUBCOMP;
    case SUBSCRIBE;
    case SUBACK;
    case UNSUBSCRIBE;
    case UNSUBACK;
    case PINGREQ;
    case PINGRESP;
    case DISCONNECT;

    /** 
     * @throws \TypeError
     */
    public function toByte(null|PublishParams $p = null): int
    {
        return match ($this) {
            self::CONNECT => 0x10,
            self::CONNACK => 0x20,
            self::PUBLISH => self::publishPackage($p),
            self::PUBACK => 0x40,
            self::PUBREC => 0x50,
            self::PUBREL => 0x62,
            self::PUBCOMP => 0x70,
            self::SUBSCRIBE => 0x82,
            self::SUBACK => 0x90,
            self::UNSUBSCRIBE => 0xa2,
            self::UNSUBACK => 0xb0,
            self::PINGREQ => 0xc0,
            self::PINGRESP => 0xd0,
            self::DISCONNECT => 0xe0,
        };
    }

    public static function fromByte(int $byte): self
    {
        return match (true) {
            $byte >= 0xe0 => self::DISCONNECT,
            $byte >= 0xd0 => self::PINGRESP,
            $byte >= 0xc0 => self::PINGREQ,
            $byte >= 0xb0 => self::UNSUBACK,
            $byte >= 0xa0 => self::UNSUBSCRIBE,
            $byte >= 0x90 => self::SUBACK,
            $byte >= 0x80 => self::SUBSCRIBE,
            $byte >= 0x70 => self::PUBCOMP,
            $byte >= 0x60 => self::PUBREL,
            $byte >= 0x50 => self::PUBREC,
            $byte >= 0x40 => self::PUBACK,
            $byte >= 0x30 => self::PUBLISH,
            $byte >= 0x20 => self::CONNACK,
            $byte >= 0x10 => self::CONNECT,
        };
    }

    private static function publishPackage(null|PublishParams $p): int
    {
        if (is_null($p)) {
            throw new \RuntimeException("PUBLISH Package must get PablishParams");
        }

        return 0x30 + self::publishFlag($p);

    }

    private static function publishFlag(PublishParams $p): int
    {
        return (int)$p->isDup * 8 + (int)$p->isRetain * 1 + ($p->QoS << 1);
    }
}
