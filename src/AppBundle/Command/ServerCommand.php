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
        # A JEDNAK NIE POTRZEBUJĘ WSTRZYKIWAĆ SERWISU. MYŚLAŁEM ŻE POTRZEBUJĘ WSTRZYKNĄĆ PARAMETRY (host i port) DO KONSTRUKTORA KLASY CHATU.
//        $chat = $this->getContainer()->get('chat');
        $port = 8080;


        if($this->getContainer()->hasParameter('chat_port')) {
            $port = $this->getContainer()->getParameter('chat_port');
        };

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat()
                )
            ),
            $port
        );

        $server->run();
    }
}

