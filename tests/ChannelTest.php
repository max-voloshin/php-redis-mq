<?php


namespace MaxVoloshin\PHPRedisMQ\Tests;


use MaxVoloshin\PHPRedisMQ\Channel;

class ChannelTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Channel name must be string
     * @dataProvider getInvalidNames
     */
    public function testInvalidName($name)
    {
        new Channel($name);
    }

    public function getInvalidNames()
    {
        $helper = new Helper();

        return $helper->getInvalidStrings();
    }
}
