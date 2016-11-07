<?php

namespace Marks12\SocketServerBundle\Command;

use Doctrine\ORM\EntityManager;
use Marks12\SocketServerBundle\DependencyInjection\Configuration;
use Marks12\SocketServerBundle\Server\Server;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ServerStartCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('socket:server:start')
            ->setDescription('Start socket server')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $server = new Server($output, $em, $this->getContainer());
        $server->up();
    }


}
