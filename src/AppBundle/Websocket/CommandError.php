<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2017-05-20
 * Time: 13:24
 */

namespace AppBundle\Websocket;


use AppBundle\Exception\WrongFlagException;

class CommandError extends AbstractWebsocketCommand implements CommandErrorInterface
{
    const ERR_FLAG = "error";
    const ERR_FLAG_MSG = "Chat nie może działać poprawnie.";
    const ERR_MSG = "Nieznany Błąd. Chat nie może działać poprawnie.";

    /**
     * @param \Exception $e
     * @return string
     */
    public function buildMessage(\Exception $e)
    {
        if($e instanceof WrongFlagException){
            $jsonMsg = $this->prepareMessage(self::ERR_FLAG, '"'.self::ERR_FLAG_MSG.'"');
            return $jsonMsg;
        }else{
            $jsonMsg = $this->prepareMessage(self::ERR_FLAG, '"'.self::ERR_MSG.'"');
            return $jsonMsg;
        }
    }
}