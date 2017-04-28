<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends Controller
{
    /**
     * @Route("/chat", name="chat")
     */
    public function indexAction()
    {
        return $this->render('chat/index.html.twig');
    }
}
