<?php

namespace Losev\MqttClient;

use Amp\Socket\Socket;
use Amp\Socket\SocketAddress;
use Losev\MqttClient\ControlPackageParams\ConnectionParam;
use Losev\MqttClient\Heandlers\ConnectionHeandler;
use Losev\MqttClient\Storages\InMemoryStorage;
use Losev\MqttClient\Storages\Storage;
use Amp\Cancellation;
use Amp\Socket\ConnectContext;
use function Amp\Socket\connect;
use function Amp\async;

class MqttClient 
{
    /** 
     * @var array<string, callable(string, string): void> 
     */
    private array $subscriptions = [];

    private null|Socket $soket = null;

    private readonly ConnectionHeandler $connectionHeandler;


    public function __construct(
        ConnectionParam $connectionParam,
        private readonly string|SocketAddress $socketAddress,
        private readonly null|Cancellation $token = null,
        private readonly null|ConnectContext $connectContext = null,
        private readonly Storage $storage = new InMemoryStorage(),
    ) {
        $this->connectionHeandler = new ConnectionHeandler($connectionParam);
    }

    public function connect(): void
    {
        $this->soket = connect($this->socketAddress, $this->connectContext, $this->token);

        $package = $this->connectionHeandler->buildConnectionPackage();
        $this->soket->write($package);
        $firstByte = $this->soket->read($this->token, 1);

        if (is_null($firstByte) || strlen($firstByte) === 0) {
            throw new \RuntimeException("failed connection");
        }

        $packageType = PackageType::fromFirestByte($firstByte);

        if ($packageType !== PackageType::CONNACK) {
            throw new \RuntimeException('strange behavior');
        }

        $bytes = $this->soket->read(
            $this->token,
            $this->connectionHeandler->responseLength(),
        );

        if (is_null($bytes) || strlen($bytes) === 0) {
            throw new \RuntimeException("strange behavior");
        }

        $this->connectionHeandler->responseHandler($bytes);
    }

    private function getSocket(): Socket
    {
        if (is_null($this->soket)) {
            throw new \RuntimeException("Socket is null");
        }

        return $this->soket;
    }
}
