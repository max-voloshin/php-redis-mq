<?php


namespace MaxVoloshin\PHPRedisMQ;


class Producer
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Channel
     */
    private $channel;

    /**
     * @param Client $client
     * @param Channel $channel
     */
    public function __construct(Client $client, Channel $channel)
    {
        $this->client = $client;
        $this->channel = $channel;
    }

    /**
     * @param Message $message
     */
    public function produce(Message $message)
    {
        $this->client->lpush($this->channel->getName(), array($message->getContent()));
    }

}
