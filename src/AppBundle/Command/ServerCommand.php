<?php
use Ratchet\Server\IoServer;
use AppBundle\Websocket\Chat;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/*
 *  Tu tak robili: http://stackoverflow.com/questions/17529657/how-to-use-properly-websockets-in-symfony2
 */
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
        $chat = $this->getContainer()->get('chat');
        $server = IoServer::factory(new HttpServer(new WsServer($chat)), 8080);
        $server->run();
    }
}

