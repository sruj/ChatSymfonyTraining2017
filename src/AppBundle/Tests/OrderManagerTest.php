<?php
/**
 * Created by PhpStorm.
 * User: chiny
 * Date: 2016-12-07
 * Time: 14:42
 */

namespace AppBundle\Tests\Utils\Order;

use AppBundle\Entity\Zamowienie;
use AppBundle\Utils\Order\OrderManager;
use AppBundle\Entity\Klient;
use AppBundle\Entity\Status;
use AppBundle\Entity\Ksiazka;
use Doctrine\Common\Collections\Collection;
use My\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Doctrine\ORM\EntityManager;
use AppBundle\Event\OrderPlacedEvent;
use AppBundle\Exception\OrderNotFoundException;

class OrderManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testPlaceOrderReturnOrderForGivenCart()
    {
        $em = $this->getEntityManagerMock();
        $storage = $this->getTokenStorageMock();
        $checker = $this->getAuthorizationCheckerMock();
        $session = $this->getSessionMock();
        $dispatcher = $this->getEventDispatcherMock(OrderPlacedEvent::NAME);
        
        $obj = new OrderManager($em,$storage,$checker,$session,$dispatcher);
        $cart = ['111'=>'4','222'=>'3'];

        $this->assertTrue($obj->placeOrder($this->getKlientMock(),$cart));
    }


    public function testPlaceOrderReturnExceptionForGivenCart()
    {
        $this->expectException(OrderNotFoundException::class);
        $em = $this->getEntityManagerMock(true);
        $storage = $this->getTokenStorageMock();
        $checker = $this->getAuthorizationCheckerMock();
        $session = $this->getSessionMock();
        $dispatcher = $this->getEventDispatcherMock(OrderPlacedEvent::NAME);

        $obj = new OrderManager($em,$storage,$checker,$session,$dispatcher);
        $cart = ['111'=>'4','222'=>'3'];

        $obj->placeOrder($this->getKlientMock(),$cart);
    }



    protected function getKlientMock()
    {
        return $this->createMock(Klient::class); //stub (wszystko zwraca null)
    }

    protected function getUserMock()
    {
        return $this->createMock(User::class); //stub (wszystko zwraca null)
    }

    protected function getTokenStorageMock()
    {
        $user = $this->getUserMock();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->createMock(TokenStorage::class);
        $tokenStorage->method('getToken')
            ->willReturn($token);

        return $tokenStorage;
    }

    protected function getAuthorizationCheckerMock($returnIsGranted = true)
    {
        $authorizationChecker = $this
            ->getMockBuilder(AuthorizationCheckerInterface::class)
            ->setMethods(['isGranted'])
            ->disableOriginalConstructor()
            ->getMock();
        $authorizationChecker
            ->expects($this->any())
            ->method('isGranted')
            ->with('IS_AUTHENTICATED_FULLY')
            ->will($this->returnValue($returnIsGranted));

        return $authorizationChecker;
    }

    protected function getSessionMock()
    {
        $flashBag = $this->createMock(FlashBagInterface::class);
        $flashBag->method('add')
            ->willReturn(null);
        
        $session = $this->createMock(Session::class);
        $session->method('getFlashBag')
            ->willReturn($flashBag);

        return $session;
    }

    /**
     * codeCoverageIgnore
     */
    protected function getEventDispatcherMock($event, $once = true)
    {
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);

        $eventDispatcherMock->expects($once ? $this->once() : $this->never())
            ->method('dispatch')
            ->with($event);

        return $eventDispatcherMock;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getEntityManagerMock($exception=false)
    {
        $ksiazka = $this->createMock(Ksiazka::class); //stub (wszystko zwraca null)
        $status = $this->createMock(Status::class); //stub (wszystko zwraca null)
        $zamowienie = $this->createMock(Zamowienie::class);
        if($exception){$zamowienie=null;};

        $ksiazkaRepository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $ksiazkaRepository
            ->expects($this->any())
            ->method('find')
            ->will($this->returnValue($ksiazka));
        $statusRepository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $statusRepository
            ->expects($this->any())
            ->method('find')
            ->will($this->returnValue($status));
        $zamowienieRepository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $zamowienieRepository
            ->expects($this->any())
            ->method('find')
            ->will($this->returnValue($zamowienie));

        $entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->setMethods(['getRepository','persist','flush'])
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager->expects($this->any())
            ->method('getRepository')
            ->withConsecutive(
                $this->equalTo('AppBundle:Status'),
                $this->equalTo('AppBundle:Ksiazka'),
                $this->equalTo('AppBundle:Ksiazka'),
                $this->equalTo('AppBundle:Zamowienie')
            )
            ->willReturnOnConsecutiveCalls(
                $statusRepository,
                $ksiazkaRepository,
                $ksiazkaRepository,
                $zamowienieRepository
            );
        $entityManager->expects($this->any())
            ->method('persist')
            ->willReturn(null);
        $entityManager->expects($this->any())
            ->method('flush')
            ->willReturn(null);

        return $entityManager;
    }
}
