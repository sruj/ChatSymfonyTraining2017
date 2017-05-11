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
#poniższe (5 liń) ROBIĘ DLA PICU, BY ZASTOSOWAĆ CUSTOM COMMAND Z ARGUMENTAMI. DLA PICU BO TO NIE MA SENSU, I TAK MUSZĘ TE PARAMETRY DOSTARCZYĆ DO KONTROLERA
#DLATEGO REZYGNUJĘ Z TEGO A ZASTOSUJĘ PARAMETRY W PARAMETERS.YML
        $helper = $this->getHelper('question');
        $question1 = new Question('Provide a hostname: ', 'localhost');
        $question2 = new Question('Provide a port number: ', '8080');
        $host = $helper->ask($input, $output, $question1);
        $port = $helper->ask($input, $output, $question2);

        if($this->getContainer()->hasParameter('chat_port')) {
            $port = $this->getContainer()->getParameter('chat_port');
        }else{
            $port = 8080;
        }

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

