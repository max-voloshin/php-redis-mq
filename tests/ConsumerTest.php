<?php


namespace MaxVoloshin\PHPRedisMQ\Tests;


use MaxVoloshin\PHPRedisMQ\Channel;
use MaxVoloshin\PHPRedisMQ\Client;
use MaxVoloshin\PHPRedisMQ\Consumer;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $channelName = 'Channel';

    /**
     * @var Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var Channel
     */
    private $channel;

    /**
     * @var Consumer
     */
    private $consumer;

    /**
     * @expectedExceptionMessage Timeout is already set
     * @expectedException \LogicException
     */
    public function testDoubleTimeout()
    {
        $this->consumer->wait();
        $this->consumer->wait();
    }

    /**
     * @expectedExceptionMessage Reliably mode is already set
     * @expectedException \LogicException
     */
    public function testDoubleReliably()
    {
        $this->consumer->reliably();
        $this->consumer->reliably();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to perform ack in non reliably mode
     */
    public function testAckWithoutConsume()
    {
        $this->consumer->ack();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to perform ack without consuming message
     */
    public function testAckWithoutMessage()
    {
        $this->consumer->reliably();

        $this->consumer->consume();

        $this->consumer->ack();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to perform ack without consuming message
     */
    public function testExtraAck()
    {
        $content = 'Message';

        $this->client
            ->expects($this->once())
            ->method('rpoplpush')
            ->with(
                $this->channelName,
                $this->consumer->getWorkingChannelName()
            )
            ->will($this->returnValue($content));

        $this->consumer->reliably();

        $this->consumer->consume();

        $this->consumer->ack();

        $this->consumer->ack();

    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to consume message without ack to previous message
     */
    public function testExtraConsume()
    {
        $content = 'Message';

        $this->client
            ->expects($this->once())
            ->method('rpoplpush')
            ->with(
                $this->channelName,
                $this->consumer->getWorkingChannelName()
            )
            ->will($this->returnValue($content));

        $this->consumer->reliably();

        $this->consumer->consume();

        $this->consumer->consume();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not unique working channel name
     */
    public function testControlUniquenessOfWorkingChannelOnConsume()
    {
        $this->client
            ->expects($this->once())
            ->method('sadd')
            ->with(
                $this->channelName,
                array($this->consumer->getWorkingChannelName())
            )
            ->will($this->returnValue(0));

        $this->consumer->reliably();

        $this->consumer->consume();
    }

    public function testReliablyConsume()
    {
        $content = 'Message';

        $this->client
            ->expects($this->once())
            ->method('rpoplpush')
            ->with(
                $this->channelName,
                $this->consumer->getWorkingChannelName()
            )
            ->will($this->returnValue($content));

        $this->client
            ->expects($this->once())
            ->method('sadd')
            ->with(
                $this->channelName,
                array($this->consumer->getWorkingChannelName())
            )
            ->will($this->returnValue(1));

        $this->consumer->reliably();

        $message = $this->consumer->consume();

        $this->assertInstanceOf(
            '\MaxVoloshin\PHPRedisMQ\Message',
            $message,
            "Consumer have to return message instance from channel when message is exists"
        );

        $this->assertSame($content, $message->getContent());
    }

    public function testAck()
    {
        $content = 'Message';

        $this->client
            ->expects($this->once())
            ->method('rpoplpush')
            ->with(
                $this->channelName,
                $this->consumer->getWorkingChannelName()
            )
            ->will($this->returnValue($content));

        $this->client
            ->expects($this->once())
            ->method('rpop')
            ->with($this->consumer->getWorkingChannelName());

        $this->client
            ->expects($this->once())
            ->method('srem')
            ->with(
                $this->channelName,
                array($this->consumer->getWorkingChannelName())
            )
            ->will($this->returnValue(1));

        $this->consumer->reliably();

        $this->consumer->consume();

        $this->consumer->ack();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown working channel name
     */
    public function testControlUniquenessOfWorkingChannelOnAck()
    {
        $content = 'Message';

        $this->client
            ->expects($this->once())
            ->method('rpoplpush')
            ->with(
                $this->channelName,
                $this->consumer->getWorkingChannelName()
            )
            ->will($this->returnValue($content));

        $this->client
            ->expects($this->once())
            ->method('srem')
            ->with(
                $this->channelName,
                array($this->consumer->getWorkingChannelName())
            )
            ->will($this->returnValue(0));

        $this->consumer->reliably();

        $this->consumer->consume();

        $this->consumer->ack();
    }

    public function testReliablyConsumeWithWait()
    {
        $timeout = 1000;

        $content = 'Message';

        $this->client
            ->expects($this->once())
            ->method('brpoplpush')
            ->with(
                $this->channelName,
                $this->consumer->getWorkingChannelName(),
                $timeout
            )
            ->will($this->returnValue($content));

        $this->consumer->reliably();

        $this->consumer->wait($timeout);

        $message = $this->consumer->consume();

        $this->assertInstanceOf(
            '\MaxVoloshin\PHPRedisMQ\Message',
            $message,
            "Consumer have to return message instance from channel when message is exists"
        );

        $this->assertSame($content, $message->getContent());
    }

    public function testConsumeEmptyChannel()
    {
        $this->client
            ->expects($this->once())
            ->method('rpop')
            ->with($this->channelName)
            ->will($this->returnValue(null));

        $this->assertNull($this->consumer->consume(), "Consume have to return 'null' from empty channel");
    }

    public function testConsumeChannelWithMessage()
    {

        $content = 'Message';

        $this->client
            ->expects($this->once())
            ->method('rpop')
            ->with($this->channelName)
            ->will($this->returnValue($content));

        $message = $this->consumer->consume();

        $this->assertInstanceOf(
            '\MaxVoloshin\PHPRedisMQ\Message',
            $message,
            "Consumer have to return message instance from channel when message is exists"
        );

        $this->assertSame($content, $message->getContent());

    }

    public function testConsumeWithWait()
    {

        $content = 'Message';

        $timeout = 1000;

        $this->client
            ->expects($this->once())
            ->method('brpop')
            ->with($this->channelName, $timeout)
            ->will($this->returnValue($content));

        $this->consumer->wait($timeout);

        $message = $this->consumer->consume();

        $this->assertInstanceOf(
            '\MaxVoloshin\PHPRedisMQ\Message',
            $message,
            "Consumer have to return message instance from channel when message is exists"
        );

        $this->assertSame($content, $message->getContent());

    }

    public function testConsumeWithIndefinitelyWait()
    {

        $content = 'Message';

        $this->client
            ->expects($this->once())
            ->method('brpop')
            ->with($this->channelName, 0)
            ->will($this->returnValue($content));

        $this->consumer->wait();

        $message = $this->consumer->consume();

        $this->assertInstanceOf(
            '\MaxVoloshin\PHPRedisMQ\Message',
            $message,
            "Consumer have to return message instance from channel when message is exists"
        );

        $this->assertSame($content, $message->getContent());

    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Timeout must be integer
     * @dataProvider getInvalidTimeouts
     */
    public function testWaitInvalidTimeout($timeout)
    {
        $this->consumer->wait($timeout);
    }

    public function getInvalidTimeouts()
    {
        $helper = new Helper();
        return $helper->getInvalidIntegers();
    }

    public function testWorkingChannelName()
    {
        $this->assertSame($this->consumer->getWorkingChannelName(), $this->consumer->getWorkingChannelName());
    }

    protected function setUp()
    {
        $this->channel = new Channel($this->channelName);
        $this->client = $this->getMock('\MaxVoloshin\PHPRedisMQ\Client');
        $this->consumer = new Consumer($this->client, $this->channel);
    }
}
