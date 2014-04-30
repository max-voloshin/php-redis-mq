<?php


namespace MaxVoloshin\PHPRedisMQ\Tests;


use MaxVoloshin\PHPRedisMQ\Channel;
use MaxVoloshin\PHPRedisMQ\Message;
use MaxVoloshin\PHPRedisMQ\Producer;

class ProducerTest extends \PHPUnit_Framework_TestCase
{

    public function testProduce()
    {
        $channelName = 'Channel';
        $channel = new Channel($channelName);

        $messageContent = 'Message';
        $message = new Message($messageContent);

        $client = $this->getMock('\MaxVoloshin\PHPRedisMQ\Client');

        $client->expects($this->once())
            ->method('lpush')
            ->with($channelName, array($messageContent))
            ->will($this->returnValue(1));

        $producer = new Producer($client, $channel);
        $producer->produce($message);
    }
}
