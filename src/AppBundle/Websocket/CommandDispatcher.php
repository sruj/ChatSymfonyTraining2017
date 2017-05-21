<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2017-05-20
 * Time: 13:24
 */

namespace AppBundle\Websocket;


use AppBundle\Exception\WrongFlagException;

class CommandDispatcher
{
    /**
     * @param $flag
     * @return CommandInterface
     * @throws WrongFlagException
     */
    public function pickCommand(string $flag): CommandInterface
    {
        if ($flag == "newUsername") {       //{"message":{ "flag":"newUsername", "data": {"68":"Dantuta"}}};
            return new NewUsernameCommand();
        }

        if ($flag == "chatMessage") {       //{"message":{ "flag":"chatMessage", "data": {"username":"Danuta","chatMessage":"cześć, co słychać?"}}};
            return new NewMessageCommand();
        }

        if ($flag == "removeUsername") {
            return new RemoveUsernameCommand();
        }

        throw new WrongFlagException('User send incorrect message with unrecognizable flag');
    }
}