<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2017-05-20
 * Time: 19:56
 */

namespace AppBundle\Websocket;


interface CommandInterface
{
    /**
     * @param $from
     * @param $msg
     * @param Chat $chat
     * @return mixed
     */
    function buildMessage($from, $msg, Chat &$chat);

}