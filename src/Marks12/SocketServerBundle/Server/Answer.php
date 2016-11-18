<?php

namespace Marks12\SocketServerBundle\Server;

/**
 *
 * User: tsv
 * Date: 09.11.16
 * Time: 10:23
 */

class Answer
{
    private $socket;

    public function __construct($socket)
    {
        $this->socket = $socket;
    }

    public function answer($string)
    {
        $string .= PHP_EOL;
        socket_write($this->socket, $string, mb_strlen($string));
    }
}