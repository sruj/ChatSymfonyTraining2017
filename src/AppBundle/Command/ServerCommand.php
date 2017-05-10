<?php

namespace AppBundle\Command;

use Ratchet\Server\IoServer;
use AppBundle\Websocket\Chat;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('chat:server')
            ->setDescription('Start the Chat server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat()
                )
            ),
            8080
        );

        $server->run();
    }
}

