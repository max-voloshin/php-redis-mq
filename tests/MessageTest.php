<?php


namespace MaxVoloshin\PHPRedisMQ\Tests;

use MaxVoloshin\PHPRedisMQ\Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Message content must be string
     * @dataProvider getInvalidContents
     */
    public function testInvalidContent($content)
    {
        new Message($content);
    }

    public function getInvalidContents()
    {
        $helper = new Helper();

        return $helper->getInvalidStrings();
    }
}
