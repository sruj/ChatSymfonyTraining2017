<?php

namespace AppBundle\Websocket;

use Psr\Log\LoggerInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


/**
 * Class Chat
 *
 * - This class listen 4 events
 */
class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $userlist;


    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->userlist = [];
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
        $this->debug('onMessage');
        echo "onMessage";
        $msgDecode = json_decode($msg);
        $flag = $msgDecode->message->flag;
        $userId = $from->resourceId;

        if($flag == "newUsername"){
            $this->debug('newUsername');
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


    /**
     * Called when a Connection is closed
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
        $this->removeClient($conn->resourceId);
        $jsonUserList = json_encode($this->userlist);
        $jsonMsg = $this->prepareMessage("userlist", $jsonUserList);
        $this->sendUserListToEveryClient($jsonMsg);
    }


    /**
     * Called when an error occurs on a Connection
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }


    private function addNewUsernameToUserList($username,$userId)
    {
        $this->userlist[$userId] = $username;
        return true;
    }


    private function removeClient($userId)
    {
        unset($this->userlist[$userId]);
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
            echo "send! ({$jsonMsg})\n";
            $this->debug($jsonMsg);
        }
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

}