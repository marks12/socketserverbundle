<?php

namespace Marks12\SocketServerBundle;

/**
 * Created by PhpStorm.
 * User: tsv
 * Date: 08.11.16
 * Time: 15:42
 */

class Client
{
    private $server_name;
    private $server_port;
    private $socket;
    private $hello_message = 'init connection';

    public function __construct($server = '127.0.0.1', $port = 10000)
    {
        $this->server_name = $server;
        $this->server_port = $port;
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($this->socket === false) {
            throw new \Exception("Cannot socket_create(): reason: " . socket_strerror(socket_last_error()));
        }

        $this->connect();
        $this->hello();
    }

    private function connect() {

        $result = socket_connect($this->socket, $this->server_name, $this->server_port);
        if ($result === false) {
            throw new \Exception("Cannot socket_connect(): reason: " . socket_strerror(socket_last_error($this->socket)));
        }
    }

    private function hello() {

        socket_write($this->socket, $this->hello_message, strlen($this->hello_message));
        while ($out = socket_read($this->socket, 2048)) {
            echo $out;
        }
    }

    public function send(string $request)
    {
        $response = '';

        socket_write($this->socket, $request, strlen($request));

        while ($out = socket_read($this->socket, 2048)) {
            $response .= $out;
        }

        return $response;
    }

    private function __destruct()
    {
        $this->send('exit');
        socket_close($this->socket);
    }
}