<?php

namespace Losev\MqttClient;

enum PackageType
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

    public static function fromFirestByte(string $byte): self
    {
        $ibyte = ord($byte[0]);
        return match (true) {
            $ibyte >= 0xe0 => self::DISCONNECT,
            $ibyte >= 0xd0 => self::PINGRESP,
            $ibyte >= 0xc0 => self::PINGREQ,
            $ibyte >= 0xb0 => self::UNSUBACK,
            $ibyte >= 0xa0 => self::UNSUBSCRIBE,
            $ibyte >= 0x90 => self::SUBACK,
            $ibyte >= 0x80 => self::SUBSCRIBE,
            $ibyte >= 0x70 => self::PUBCOMP,
            $ibyte >= 0x60 => self::PUBREL,
            $ibyte >= 0x50 => self::PUBREC,
            $ibyte >= 0x40 => self::PUBACK,
            $ibyte >= 0x30 => self::PUBLISH,
            $ibyte >= 0x20 => self::CONNACK,
            $ibyte >= 0x10 => self::CONNECT,
        };
    }
}
