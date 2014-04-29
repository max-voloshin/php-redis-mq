<?php


namespace MaxVoloshin\PHPRedisMQ;


interface Client
{
    /**
     * @link http://redis.io/commands/lpush
     * @param string $key
     * @param string[] $values
     * @return integer
     */
    public function lpush($key, array $values);

    /**
     * @link http://redis.io/commands/rpop
     * @param string $key
     * @return null|string
     */
    public function rpop($key);

    /**
     * @link http://redis.io/commands/brpop
     * @param string $key
     * @param integer $timeout (milliseconds)
     * @return null|string
     */
    public function brpop($key, $timeout);

    /**
     * @link http://redis.io/commands/rpoplpush
     * @param string $source
     * @param string $destination
     * @return null|string
     */
    public function rpoplpush($source, $destination);

    /**
     * @link http://redis.io/commands/brpoplpush
     * @param string $source
     * @param string $destination
     * @param integer $timeout (seconds)
     * @return null|string
     */
    public function brpoplpush($source, $destination, $timeout);

}
