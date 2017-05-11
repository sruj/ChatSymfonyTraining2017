<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\CssSelector\Tests\Parser\ReaderTest;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends Controller
{

    /**
     * @Route("/chat", name="chat")
     */
    public function chatAction(Request $request)
    {
        /** @var Form $form */
        $form = $this->createFormBuilder()
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('send', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $username = $data['name'];

            if(!($this->getParameter('chat_host')||$this->getParameter('chat_port'))) {
//TODO:                throw jakiś custom exception że trzeba podać parametry host i port w appbundle/../parameters.yml
            }

            $port = $this->getParameter('chat_port');
            $host = $this->getParameter('chat_host');

            return $this->render('@App/chat/chat.html.twig', ['username'=>$username, 'host'=>$host, 'port'=>$port]);
        }

        return $this->render('@App/chat/login.html.twig',['form' => $form->createView()]);


    }

}
