<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2017-05-13
 * Time: 13:24
 */

namespace AppBundle\Websocket;


use AppBundle\Exception\WrongFlagException;
use Ratchet\ConnectionInterface;

class ChatCommand extends Debugger
{
    protected $userlist;
    protected $clients;

    const ERR_FLAG = "error";
    const ERR_FLAG_MSG = "Chat nie może działać poprawnie.";
    const ERR_MSG = "Nieznany Błąd. Chat nie może działać poprawnie.";

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->userlist = [];
    }

    /**
     * @param ConnectionInterface $from
     * @param $msg
     * @throws WrongFlagException
     */
    protected function send(ConnectionInterface $from, $msg)
    {
        $msgDecode = json_decode($msg);
        $flag = $msgDecode->message->flag;
        $userId = $from->resourceId;

        if ($flag == "newUsername") {
            $username = $msgDecode->message->data;
            $this->addNewUsernameToUserList($username, $userId);
            $jsonUserList = json_encode($this->userlist);
            $jsonMsg = $this->prepareMessage("userlist", $jsonUserList);
            $this->sendUserListToEveryClient($jsonMsg);
            return;
        }

        if ($flag == "chatMessage") {
            $numRecv = count($this->clients) - 1;
            echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
                , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
            foreach ($this->clients as $client) {
                $client->send($msg);
            }
            return;
        }

        throw new WrongFlagException('User send incorrect message with unrecognizable flag');

    }

    protected function addNewUsernameToUserList($username,$userId)
    {
        $this->userlist[$userId] = $username;
        return true;
    }


    protected function removeClient($userId)
    {
        unset($this->userlist[$userId]);
    }


    /**
     * @param $flag
     * @param $data
     * @return string
     */
    protected function prepareMessage($flag, $data): string
    {
        $jsonMsg = '{"message":{ "flag":"' . $flag . '", "data": ' . $data . '}}';
        return $jsonMsg;
    }


    /**
     * @param $jsonMsg
     */
    protected function sendUserListToEveryClient($jsonMsg)
    {
        foreach ($this->clients as $client) {
            $client->send($jsonMsg);
            echo "send! ({$jsonMsg})\n";
            $this->debug($jsonMsg);
        }
    }


    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    protected function sendErrorMessage(ConnectionInterface $conn, \Exception $e)
    {
        if($e instanceof WrongFlagException){
            $jsonMsg = $this->prepareMessage(self::ERR_FLAG, '"'.self::ERR_FLAG_MSG.'"');
            $conn->send($jsonMsg);
        }else{
            $jsonMsg = $this->prepareMessage(self::ERR_FLAG, '"'.self::ERR_MSG.'"');
            $conn->send($jsonMsg);
        }
    }
}