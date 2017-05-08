<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\CssSelector\Tests\Parser\ReaderTest;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends Controller
{
//    /**
//     * @Route("/chat", name="chat")
//     */
//    public function indexAction()
//    {
//        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
//            throw $this->createAccessDeniedException();
//        }
//
//        $user = $this->getUser();
//
//        return $this->render('@App/chat/index.html.twig');
//    }

    /**
     * @Route("/chat", name="chat")
     */
    public function chatAction(Request $request)
    {
        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('send', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $username = $data['name'];
            return $this->render('chat.html.twig', ['username'=>$username]);
        }

        return $this->render('@App/chat/chat.html.twig',['form' => $form->createView()]);


    }

}
