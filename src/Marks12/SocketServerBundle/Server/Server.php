<?php
/**
 *
 * User: tsv
 * Date: 25.10.16
 * Time: 16:44
 */

namespace Marks12\SocketServerBundle\Server;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Marks12\SocketServerBundle\DependencyInjection\Configuration;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;

class Server
{
    /** @var EntityManager */
    private $em;

    private $address;
    private $port;
    private $sock;
    private $container;

    const ERROR_SOCKET_CREATE   = 'Cant execute socket create';
    const ERROR_SOCKET_BIND     = 'Cant execute socket bind';
    const ERROR_SOCKET_LISTEN   = 'Cant execute socket listen';
    const ERROR_SOCKET_ACCEPT   = 'Cant execute socket accept';
    const ERROR_SOCKET_READ     = 'Cant execute socket read';
    const WELCOME_MESSAGE       = 'Welcome to server: Send exit for disconnect';

    const CONNECTIONS_COUNT     = 50;

    const FORK_ERROR            = -1;

    public function __construct(OutputInterface $output, EntityManager $em, $container)
    {
        $this->container = $container;
        $this->output = $output;
        $this->em = $em;

        $this->address = $this->container->getParameter('marks12_socket_server.address');
        $this->port = $this->container->getParameter('marks12_socket_server.port');
    }

    public function getSocketApiHandler()
    {
        $config = $this->container->getParameter('marks12_socket_server.class');

        return $config;
    }

    public function up()
    {

        $socketApi = $this->getSocketApiHandler();

        if(!class_exists($socketApi)) {
            throw new Exception(
                'Your class ' .
                $socketApi .
                ' not found. Please set absolute class path like: AppBundle\Folder\ClassName'
            );
        }

        if(!method_exists($socketApi, 'run')) {
            throw new Exception(
                'Your class ' .
                $socketApi .
                ' found but not contain method run. Please define public function run($data, $em) {}'
            );
        }

        $this->init();
        $clients = [$this->sock];

        while (true) {

            usleep(1000);

            // create a copy, so $clients doesn't get modified by socket_select()
            $read = $clients;
            $write = $clients;
            $except = $clients;

            // get a list of all the clients that have data to be read from
            // if there are no clients with data, go to next iteration
            if (socket_select($read, $write, $except, 0) < 1) {
                continue;
            }

            // check if there is a client trying to connect
            if (in_array($this->sock, $read)) {
                // accept the client, and add him to the $clients array
                $newsock = socket_accept($this->sock);
                socket_set_nonblock($newsock);

                $clients[] = $newsock;

                // send the client a welcome message
                $this->sendWelcome($newsock);

                socket_getpeername($newsock, $ip);
                echo "New client connected: {$ip}\n"; // to server_log

                $key = array_search($this->sock, $read);
                unset($read[$key]);
            }

            // loop through all the clients that have data to read from
            foreach ($read as $read_sock) {
                // read until newline or 1024 bytes
                // socket_read while show errors when the client is disconnected, so silence the error messages
                $data = @socket_read($read_sock, 1024, PHP_NORMAL_READ);

                // check if the client is disconnected
                if ($data == false) {
                    // remove client for $clients array
                    $key = array_search($read_sock, $clients);
                    socket_close($read[$key]);
                    unset($clients[$key]);
                    unset($read[$key]);
                    echo "client disconnected.\n";
                    // continue to the next client to read from, if any
                    continue;
                }

                $data = trim($data);

                if (!empty($data)) {
                    foreach ($clients as $client_key => $send_sock) {
                        // answer who ask
                        if ($send_sock == $read_sock) {

                            if ($data == 'halt') {


                                foreach($clients as $kk=>$c) {
                                    unset($clients[$kk]);
                                }
                                socket_close($this->sock);
                                echo "socket shutted down\n";
                                exit();
                            }

                            if ($data == 'exit') {
                                socket_close($send_sock);
                                unset($clients[$client_key]);
                                continue;
                            }

                            $this->em->getConnection()->close();
                            $pid = pcntl_fork();

                            if ($pid == self::FORK_ERROR) {
                                throw new \Exception('Cant fork process');
                                //error
                            } elseif ($pid) {
                                //parent process
                                //сюда попадет родительский процесс
                                echo "receive command $data \n";

                                if ($data == 'long') {
                                    sleep(10);
                                }
                                if ($data == 'short') {
                                    sleep(1);
                                }

                                $this->em->getConnection()->connect();
                                $object = new $socketApi($this->container);
                                $answer = new Answer($send_sock, $clients);
                                $object->run($data, $this->em, $answer);
                                $this->em->getConnection()->close();
//                                posix_kill(posix_getpid(), SIGKILL);
                                exit('finish command PID:' . posix_getpid() . ' ' . PHP_EOL);

                            } else {
                                //а сюда — дочерний процесс
                                //child process
                            }
                        }
                    }
                }
            }
        }

        socket_close($this->sock);
    }

    public function send($client, string $message)
    {
        $message .=  "\r\n";

        if ($client) {
            socket_write($client, $message , strlen($message));
        }
    }

    private function sendWelcome($client)
    {
        $this->send($client, self::WELCOME_MESSAGE);
    }

    private function init()
    {
        set_time_limit(0);

        if (($this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            $this->socketError(self::ERROR_SOCKET_CREATE);
        }

        if (socket_bind($this->sock, $this->address, $this->port) === false) {
            $this->socketError(self::ERROR_SOCKET_BIND);
        }

        if (socket_listen($this->sock, self::CONNECTIONS_COUNT) === false) {
            $this->socketError(self::ERROR_SOCKET_LISTEN);
        }

        socket_set_nonblock($this->sock);
    }

    private function socketError($error)
    {
        $this->output->writeln(
            $error .
            ": reason: " .
            socket_strerror(
                socket_last_error($this->sock)
            )
        );
    }
}