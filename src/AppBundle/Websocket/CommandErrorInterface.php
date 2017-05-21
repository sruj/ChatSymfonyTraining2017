<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2017-05-21
 * Time: 14:32
 */

namespace AppBundle\Websocket;


interface CommandErrorInterface
{
    /**
     * @param \Exception $e
     * @return mixed
     */
    public function buildMessage(\Exception $e);
}