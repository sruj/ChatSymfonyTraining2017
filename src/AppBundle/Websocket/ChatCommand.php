<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2017-05-13
 * Time: 13:24
 */

namespace AppBundle\Websocket;


class ChatCommand
{
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

    public $debugCounter = 0;
    public function debug($from)
    {
        $this->debugCounter++;
        $filename= (string)date('Y-m-d_H.i.s');
        ob_start();
        var_dump($from);
        $result = ob_get_clean();
        file_put_contents(__DIR__ .'_'.$filename.'_('.$this->debugCounter.').txt', $result);
    }

}