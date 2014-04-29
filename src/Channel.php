<?php


namespace MaxVoloshin\PHPRedisMQ;


class Channel
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        \Assert\that($name)->string("Channel name must be string");

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}
