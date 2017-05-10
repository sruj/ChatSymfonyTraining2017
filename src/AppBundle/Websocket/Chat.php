<?php

namespace AppBundle\Websocket;

use Psr\Log\LoggerInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

//TODO: POPRAWIĆ KOMENTARZE, POUSUWAĆ TO I TAMTO

/**
 * Class Chat
 *
 * - This class listen 4 events
 */
class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $userlist;
    private $logger;


    /**
     * Chat constructor.
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->userlist = [];

        $this->logger = new Logger('name');
    }


    /**
     * Called when a new client has Connected.
     * Store the new connection to send messages to later.
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }



    /**
     * Called when a message is received by a Connection
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $msgDecode = json_decode($msg);
        $flag = $msgDecode->message->flag;
        $userId = $from->resourceId;

        if($flag == "newUsername"){
            $username = $msgDecode->message->data;
            $this->addNewUsernameToUserList($username,$userId);
            $jsonUserList = json_encode($this->userlist);
            $jsonMsg = $this->prepareMessage("userlist", $jsonUserList);
            $this->sendUserListToEveryClient($jsonMsg);

            return;
        }

        if($flag == "chatMessage") {
            $numRecv = count($this->clients) - 1;
            echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
                , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

            foreach ($this->clients as $client) {
                $client->send($msg);
            }
        }
    }


    private function addNewUsernameToUserList($username,$userId)
    {
        //logger. Zbędne raczej.
//        $this->logger->pushHandler(new StreamHandler(__DIR__.'chryp.log', Logger::NOTICE));
//        $this->logger->notice($message);

        $this->userlist[$userId] = $username;

        return true;
    }



    /**
     * Called when a Connection is closed
     */
    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";

        $this->removeClient($conn->resourceId);
        $jsonUserList = json_encode($this->userlist);
        $jsonMsg = $this->prepareMessage("userlist", $jsonUserList);
        $this->sendUserListToEveryClient($jsonMsg);

    }

    private function removeClient($userId){
        //usuń element z tablicy
        unset($this->userlist[$userId]);
    }


    /**
     * Called when an error occurs on a Connection
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }











    private $debugCounter = 0;
    private function debug($from)
    {
        $this->debugCounter++;
        $filename= (string)date('Y-m-d_H.i.s');
        ob_start();
        var_dump($from);
        $result = ob_get_clean();
        file_put_contents(__DIR__ .'_'.$filename.'_('.$this->debugCounter.').txt', $result);
    }

    /**
     * @param $flag
     * @param $data
     * @return string
     */
    private function prepareMessage($flag, $data): string
    {
        $jsonMsg = '{"message":{ "flag":"' . $flag . '", "data": ' . $data . '}}';
        return $jsonMsg;
    }

    /**
     * @param $jsonMsg
     */
    private function sendUserListToEveryClient($jsonMsg)
    {
        foreach ($this->clients as $client) {
            $client->send($jsonMsg);
        }
    }


}