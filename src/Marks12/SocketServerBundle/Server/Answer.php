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
    private $clients;

    public function __construct($socket, $clients)
    {
        $this->socket = $socket;
        $this->clients = $clients;
    }

    public function answer($string)
    {
        $string .= PHP_EOL;
        socket_write($this->socket, $string, mb_strlen($string));
    }

    public function sendToAllClients($string)
    {
        foreach ($this->clients as $key => $client) {
            if (!$key || $client == $this->socket) {
                continue;
            }

            $string = PHP_EOL . $string . PHP_EOL;
            socket_write($client, $string, mb_strlen($string));
        }
    }
}
