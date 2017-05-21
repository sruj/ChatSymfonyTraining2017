<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2017-05-20
 * Time: 13:17
 */

namespace AppBundle\Websocket;


class NewUsernameCommand extends AbstractWebsocketCommand implements CommandInterface
{
    protected $chat;
    protected $clients;


    /**
     * @param $from
     * @param $msg
     * @param Chat $chat
     * @param \Exception|null $e
     * @return string
     */
    public function buildMessage($from, $msg, Chat &$chat, \Exception $e = null)
    {
        $msgDecode = json_decode($msg);
        $userId = $from->resourceId;
        $this->chat = $chat;
        $username = $msgDecode->message->data;

        $this->addUsername($username, $userId);
        $jsonUserList = json_encode($this->chat->userlist);
        $jsonMsg = $this->prepareMessage("userlist", $jsonUserList);

        return $jsonMsg;
    }


    /**
     * @param $username
     * @param $userId
     */
    private function addUsername($username, $userId)
    {
        $this->chat->userlist[$userId] = $username;
    }


}