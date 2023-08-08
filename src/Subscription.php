<?php

namespace Losev\MqttClient;

class Subscription 
{
    /** 
     * @var non-empty-string 
     */
    private string $regexifiedTopicFilter;

    /** 
     * @param \Closure(string, string): void $fn
     * @param non-empty-string $topic
     */
    public function __construct(
        private string $topic,
        private int $QoS,
        private \Closure $fn,
    ) {
        if (
            strpos($topic, '$share/') === 0
            && ($separatorIndex = strpos($topic, '/', 7)) !== false
        ) {
            $topic = substr($topic, $separatorIndex + 1);
        }

        $this->regexifiedTopicFilter = '/^'
            . str_replace(
                ['$', '/', '+', '#'],
                ['\$', '\/', '([^\/]*)', '(.*)'],
                $topic,
            )
            . '$/';
    }

    public function match(string $topic): bool
    {
        return (bool)preg_match($this->regexifiedTopicFilter, $topic);
    }

    /** 
     * @return array{string, int} 
     */
    public function getPropertyForReSubscribe(): array
    {
        return [$this->topic, $this->QoS];
    }

    public function call(string $topic, string $bytes): void
    {
        $fn = $this->fn;
        $fn($topic, $bytes);
    }
}
