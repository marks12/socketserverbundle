SocketServerBundle
==================

Module allow open new multi-user socket server on port 10000 and call your classes.

# Installation

Install this module via composer 

`composer require marks12/socketserverbundle`

# Configure

add to config/config.yml

```
marks12_socket_server:
    classes:    ['AppBundle\Socket\ServerProduct', 'AppBundle\Socket\ServerGhost' ]
    address:    '0.0.0.0'
    port:       '10000'
```

## Creating classes

create class in your application
`AppBundle\Socket\ServerProduct.php`

```php
<?php
namespace AppBundle\Socket;

class ServerProduct {

    function run ($data, $em, $send_sock) {

        $msg = 'Hello, this is class run in ServerProduct. Data: ' . $data . PHP_EOL;
        socket_write($send_sock, $msg, strlen($msg));

    }
}
```

`AppBundle\Socket\ServerProduct.php`

```php 
<?php

namespace AppBundle\Socket;

class ServerGhost {

    function run ($data, $em, $send_sock) {

        $msg = 'Hello, this is class run in ServerGhost. Data: ' . $data . PHP_EOL;
        socket_write($send_sock, $msg, strlen($msg));
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
