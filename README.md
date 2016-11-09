SocketServerBundle
==================

Module allow open new multi-user socket server on port 10000 
and call your class.

# Installation

Install this module via composer 

`composer require marks12/socketserverbundle`

# Configure

add to config/config.yml

```
marks12_socket_server:
    class:    'AppBundle\Socket\ResponseFactoryExample'
    address:    '0.0.0.0'
    port:       '10000'
```

## Creating class

create class in your application
`AppBundle\Socket\ResponseFactoryExample.php`

```php
<?php

namespace AppBundle\Socket;

class ResponseFactoryExample {

    function run ($data, $em, $answer_object) {
        
        $msg = 'Hello, this is class run in ServerProduct. Data: ' . 
        $data . 
        PHP_EOL;
        
        $answer_object->answer($msg);
    }
}
```

# Using

Start your socket server

`bin/console marks12:socket:start`

## Connect to server

```
telnet localhost 10000
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
Welcome to server: Send exit for disconnect


test
Hello, this is class run in ServerProduct. Data: test
Hello, this is class run in ServerGhost. Data: test


exit
Connection closed by foreign host.
```
