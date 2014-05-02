<?php


namespace MaxVoloshin\PHPRedisMQ\Tests;


use MaxVoloshin\PHPRedisMQ\Client;

class StrictClient implements Client
{
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @link http://redis.io/commands/srem
     * @param string $key
     * @param string[] $members
     * @return integer
     */
    public function srem($key, array $members)
    {
        \Assert\that($key)->string(__FUNCTION__ . ": Key must be string");
        \Assert\that($members)->all()->string(__FUNCTION__ . ": All members must be string");

        $result = $this->client->srem($key, $members);

        \Assert\that($result)->integer(__FUNCTION__ . ": Result must be integer");

        return $result;
    }

    /**
     * @link http://redis.io/commands/sadd
     * @param string $key
     * @param string[] $members
     * @return integer
     */
    public function sadd($key, array $members)
    {
        \Assert\that($key)->string(__FUNCTION__ . ": Key must be string");
        \Assert\that($members)->all()->string(__FUNCTION__ . ": All members must be string");

        $result = $this->client->sadd($key, $members);

        \Assert\that($result)->integer(__FUNCTION__ . ": Result must be integer");

        return $result;
    }

    /**
     * @link http://redis.io/commands/lpush
     * @param string $key
     * @param string[] $values
     * @return integer
     */
    public function lpush($key, array $values)
    {
        \Assert\that($key)->string(__FUNCTION__ . ": Key must be string");
        \Assert\that($values)->all()->string(__FUNCTION__ . ": All values must be string");

        $result = $this->client->lpush($key, $values);

        \Assert\that($result)->integer(__FUNCTION__ . ": Result must be integer");

        return $result;
    }

    /**
     * @link http://redis.io/commands/rpop
     * @param string $key
     * @return null|string
     */
    public function rpop($key)
    {
        \Assert\that($key)->string(__FUNCTION__ . ": Key must be string");

        $result = $this->client->rpop($key);

        \Assert\that($result)->nullOr()->string(__FUNCTION__ . ": Result of 'rpop' must be null or string");

        return $result;
    }

    /**
     * @link http://redis.io/commands/brpop
     * @param string $key
     * @param integer $timeout (milliseconds)
     * @return null|string
     */
    public function brpop($key, $timeout)
    {
        \Assert\that($key)->string(__FUNCTION__ . ": Key must be string");
        \Assert\that($timeout)->integer(__FUNCTION__ . ": Timeout must be integer");

        $result = $this->client->brpop($key, $timeout);

        \Assert\that($result)->nullOr()->string(__FUNCTION__. ": Result must be null or string");

        return $result;
    }

    /**
     * @link http://redis.io/commands/rpoplpush
     * @param string $source
     * @param string $destination
     * @return null|string
     */
    public function rpoplpush($source, $destination)
    {
        \Assert\that($source)->string(__FUNCTION__. ": Source must be string");
        \Assert\that($destination)->string(__FUNCTION__ . ": Destination must be string");

        $result = $this->client->rpoplpush($source, $destination);

        \Assert\that($result)->nullOr()->string($result);

        return $result;
    }

    /**
     * @link http://redis.io/commands/brpoplpush
     * @param string $source
     * @param string $destination
     * @param integer $timeout (seconds)
     * @return null|string
     */
    public function brpoplpush($source, $destination, $timeout)
    {
        \Assert\that($source)->string(__FUNCTION__. ": Source must be string");
        \Assert\that($destination)->string(__FUNCTION__ . ": Destination must be string");
        \Assert\that($timeout)->integer(__FUNCTION__ . ": Timeout must be integer");

        $result = $this->client->brpoplpush($source, $destination, $timeout);

        \Assert\that($result)->nullOr()->string($result);

        return $result;
    }
}