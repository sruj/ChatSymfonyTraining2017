<?php

namespace AppBundle\Command;

use Ratchet\Server\IoServer;
use AppBundle\Websocket\Chat;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use AppBundle\Entity\Connection;

class ServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('chat:server')
            ->setDescription('Start the Chat server')
            ->addArgument('host', InputArgument::OPTIONAL, 'Provide a hostname')
            ->addArgument('port', InputArgument::OPTIONAL, 'Provide a port number')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question1 = new Question('Provide a hostname: ', 'localhost');
        $question2 = new Question('Provide a port number: ', '8080');
        $host = $helper->ask($input, $output, $question1);
        $port = $helper->ask($input, $output, $question2);

        $connection = new Connection();
        $connection->setHost($host);
        $connection->setPort($port);
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($connection);
        $em->flush();

        $this->runServer($port);
    }

    /**
     * @param $port
     */
    protected function runServer($port)
    {
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

