<?php
/**
 * User: tsv
 * Date: 09.11.16
 * Time: 10:37
 */

namespace Marks12\SocketServerBundle\Server;

class ResponseFactoryExample {

    function run ($data, $em, $answer_object) {

        $msg = 'Hello, this is class run in ServerProduct. Data: ' .
            $data .
            PHP_EOL;

        $answer_object->answer($msg);
    }
}