<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\CssSelector\Tests\Parser\ReaderTest;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            return $this->render('@App/chat/chat.html.twig', ['username'=>$username]);
        }

        return $this->render('@App/chat/login.html.twig',['form' => $form->createView()]);
    }


    /**
     * ajax w chat.js pyta o nazwę hosta i port z db
     * (które zostały tam zapisane przy komendzie chat:server)
     *
     * @Route(name="server_data", options={"expose"=true})
     */
    public function serverdataAction(Request $request)
    {
        $connection = $this->getDoctrine()
            ->getRepository('AppBundle:Connection')
            ->findLatest();

        $host = $connection->getHost();
        $port = $connection->getPort();

        $arr = ['host'=>$host,'port'=>$port];
        $json = json_encode($arr);

        return new JsonResponse($json);

    }

}
