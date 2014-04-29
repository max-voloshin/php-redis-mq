<?php


namespace MaxVoloshin\PHPRedisMQ;


class Message
{

    /**
     * @var string
     */
    private $content;

    /**
     * @param string $content
     */
    public function __construct($content)
    {
        \Assert\that($content)->string("Message content must be string");

        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

}
