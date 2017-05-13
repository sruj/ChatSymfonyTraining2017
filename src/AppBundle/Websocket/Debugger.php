<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2017-05-13
 * Time: 14:21
 */

namespace AppBundle\Websocket;


class Debugger
{
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