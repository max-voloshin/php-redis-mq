<?php


namespace MaxVoloshin\PHPRedisMQ;


class Consumer
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
     * @var int|null
     */
    private $timeout;

    /**
     * @var bool
     */
    private $reliably = false;

    /**
     * @var bool
     */
    private $ackExpected = false;

    /**
     * @var string
     */
    protected $workingChannelName;

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
     * @param int $timeout
     */
    public function wait($timeout = 0)
    {
        if (isset($this->timeout)) {
            throw new \LogicException('Timeout is already set');
        }

        \Assert\that($timeout)->integer('Timeout must be integer');

        $this->timeout = $timeout;
    }

    public function reliably()
    {
        if ($this->reliably) {
            throw new \LogicException('Reliably mode is already set');
        }

        $this->reliably = true;
    }

    public function ack()
    {
        if (!$this->reliably) {
            throw new \LogicException('Unable to perform ack in non reliably mode');
        }

        if (!$this->ackExpected) {
            throw new \LogicException('Unable to perform ack without consuming message');
        }

        $this->client->rpop($this->getWorkingChannelName());

        if (0 === $this->client->srem($this->channel->getName(), array($this->getWorkingChannelName()))) {
            throw new \RuntimeException("Unknown working channel name '{$this->getWorkingChannelName()}");
        }

        $this->ackExpected = false;
    }

    /**
     * @return null|Message
     */
    public function consume()
    {
        if ($this->ackExpected) {
            throw new \LogicException('Unable to consume message without ack to previous message');
        }

        $message = null;

        if ($this->timeout === null) {
            if ($this->reliably) {
                if (0 === $this->client->sadd($this->channel->getName(), array($this->getWorkingChannelName()))) {
                    throw new \RuntimeException("Not unique working channel name '{$this->getWorkingChannelName()}'");
                }
                $content = $this->client->rpoplpush(
                    $this->channel->getName(),
                    $this->getWorkingChannelName()
                );
            } else {
                $content = $this->client->rpop($this->channel->getName());
            }
        } else {
            if ($this->reliably) {
                $content = $this->client->brpoplpush(
                    $this->channel->getName(),
                    $this->getWorkingChannelName(),
                    $this->timeout
                );
            } else {
                $content = $this->client->brpop($this->channel->getName(), $this->timeout);
            }
        }

        if ($content !== null) {

            $message = new Message($content);

            if ($this->reliably) {
                $this->ackExpected = true;
            }

        }

        return $message;
    }

    /**
     * @return string
     */
    public function getWorkingChannelName()
    {
        if (!isset($this->workingChannelName)) {
            $this->workingChannelName = $this->channel->getName() . '_' . spl_object_hash($this) . '_' . uniqid();
        }

        return $this->workingChannelName;
    }

}
