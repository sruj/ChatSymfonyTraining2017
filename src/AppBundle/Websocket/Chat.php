<?php

namespace AppBundle\Websocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;


/**
 * Class Chat
 *
 * - This class listen 4 events
 */
class Chat extends Debugger implements MessageComponentInterface
{
    public $userlist;
    public $clients;
    protected $commandError;
    protected $commandDispatcher;

    /**
     * Chat constructor.
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->userlist = [];
        $this->commandDispatcher = new CommandDispatcher();
        $this->commandError = new CommandError();
    }

    /**
     * Called when a new client has Connected.
     * Store the new connection to send messages to later.
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }


    /**
     * Called when a message is received by a Connection
     * @param ConnectionInterface $from
     * @param string $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $msgDecode = json_decode($msg);
        $flag = $msgDecode->message->flag;
        $command = $this->commandDispatcher->pickCommand($flag);
        $jsonMsg = $command->buildMessage($from,$msg,$this);
        $this->send($jsonMsg);

    }


    /**
     * Called when a Connection is closed
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
        $command = $this->commandDispatcher->pickCommand($flag='removeUsername');
        $jsonMsg = $command->buildMessage($conn,null,$this);
        $this->send($jsonMsg);
    }


    /**
     * Called when an error occurs on a Connection
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $jsonMsg = $this->commandError->buildMessage($e);
        $this->send($jsonMsg);
        $conn->close();
    }


    /**
     * @param $jsonMsg
     * @param bool $toEveryClient
     */
    protected function send($jsonMsg, $toEveryClient = true)
    {
        if($toEveryClient) {
            foreach ($this->clients as $client) {
                $client->send($jsonMsg);
//                echo "send! ({$jsonMsg})\n";
//                $this->debug($jsonMsg);
            }
        }
    }

}