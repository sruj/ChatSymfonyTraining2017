<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2017-05-13
 * Time: 13:24
 */

namespace AppBundle\Websocket;


use Ratchet\ConnectionInterface;

class ChatCommand extends Debugger
{
    protected $userlist;
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->userlist = [];
    }

    /**
     * @param ConnectionInterface $from
     * @param $msg
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
        }
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



}