<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2017-05-20
 * Time: 13:13
 */

namespace AppBundle\Websocket;


abstract class AbstractWebsocketCommand extends Debugger
{

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

}