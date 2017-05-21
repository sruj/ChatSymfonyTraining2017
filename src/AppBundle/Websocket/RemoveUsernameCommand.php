<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2017-05-20
 * Time: 13:17
 */

namespace AppBundle\Websocket;


class RemoveUsernameCommand extends AbstractWebsocketCommand implements CommandInterface
{
    protected $userlist;
    protected $clients;


    /**
     * @param $from
     * @param $msg
     * @param Chat $chat
     * @return string
     */
    public function buildMessage($from, $msg, Chat &$chat)
    {
        $userId = $from->resourceId;
        $this->userlist = $chat->userlist;

        $this->removeUsername($userId);
        $jsonUserList = json_encode($this->userlist);
        $jsonMsg = $this->prepareMessage("userlist", $jsonUserList);

        return $jsonMsg;
    }


    /**
     * @param $userId
     */
    private function removeUsername($userId)
    {
        unset($this->userlist[$userId]);
    }


}