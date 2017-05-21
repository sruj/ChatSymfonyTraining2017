<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2017-05-20
 * Time: 13:18
 */

namespace AppBundle\Websocket;



class NewMessageCommand extends AbstractWebsocketCommand implements CommandInterface
{
    /**
     * @param $from
     * @param $msg
     * @param Chat $chat
     * @return mixed
     */
    public function buildMessage($from, $msg, Chat &$chat)
    {
        $numRecv = count($chat->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        return $msg;
    }

}