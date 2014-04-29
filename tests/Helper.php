<?php


namespace MaxVoloshin\PHPRedisMQ\Tests;


class Helper
{
    public function getInvalidStrings()
    {
        return array(
            array(1),
            array(1.2),
            array(true),
            array(array(1)),
            array((object)array('property' => 'value')),
            array(null)
        );
    }

    public function getInvalidIntegers()
    {
        return array(
            array('string'),
            array(1.2),
            array(true),
            array(array(1)),
            array((object)array('property' => 'value')),
            array(null)
        );
    }
}
