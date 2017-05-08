<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    private $userlist = array(
        "a"=>"1",
        "b"=>"2",
        "c"=>"3",
    );


    /**
     * TESTUJĘ MONOLOG
     *
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
//        $json = json_decode('{"message":{ "flag":"username", "data": "jojne"}}');
//        $flag=$json->message->flag;









        $jsonUserList = json_encode($this->userlist);
        $jsonMsg = '{"message":{ "flag":"userlist", "data": '.$jsonUserList.'}}';

        $json = json_decode($jsonMsg);
        $z=0;
        $a=$json->message->data->a;
        $z=0;





















        $logger = $this->get('monolog.logger.chat');
        $logger->info('I just got the logger');
        $logger->error('An error occurred');

        $logger->critical('I left the oven on!', array(
            // include extra "context" info in your logs
            'cause' => 'in_hurry',
        ));



        return $this->render('@App/default.html.twig', array());
    }
}
