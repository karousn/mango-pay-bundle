<?php

namespace UniAlteri\MangoPayBundle\Tests\Service;

use MangoPay\ApiPayIns;
use MangoPay\PayIn;
use MangoPay\PayInExecutionDetailsDirect;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use UniAlteri\MangoPayBundle\Event\MangoPayEvents;
use UniAlteri\MangoPayBundle\Event\SecureFlowEvent;
use UniAlteri\MangoPayBundle\Service\Interfaces\StorageServiceInterface;
use UniAlteri\MangoPayBundle\Service\SecureFlowService;

/**
 * Class SecureFlowServiceTest
 * @package UniAlteri\MangoPayBundle\Tests\Service
 * @covers UniAlteri\MangoPayBundle\Service\SecureFlowService
 */
class SecureFlowServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Router
     */
    protected $routerMock;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatchedMock;

    /**
     * @var ApiPayIns
     */
    protected $mangoPayPayInsApiMock;

    /**
     * @var StorageServiceInterface
     */
    protected $storageServiceMock;

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Router
     */
    protected function getRouterMock()
    {
        if (!$this->routerMock instanceof Router) {
            $this->routerMock = $this->getMock('Symfony\Bundle\FrameworkBundle\Routing\Router', [], [], '', false);
        }

        return $this->routerMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EventDispatcherInterface
     */
    protected function getEventDispatcherInterfaceMock()
    {
        if (!$this->eventDispatchedMock instanceof EventDispatcherInterface) {
            $this->eventDispatchedMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface', [], [], '', false);
        }

        return $this->eventDispatchedMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ApiPayIns
     */
    protected function getApiPayInsMock()
    {
        if (!$this->mangoPayPayInsApiMock instanceof ApiPayIns) {
            $this->mangoPayPayInsApiMock = $this->getMock('MangoPay\ApiPayIns', [], [], '', false);
        }

        return $this->mangoPayPayInsApiMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|StorageServiceInterface
     */
    protected function getStorageServiceInterfaceMock()
    {
        if (!$this->storageServiceMock instanceof StorageServiceInterface) {
            $this->storageServiceMock = $this->getMock('UniAlteri\MangoPayBundle\Service\Interfaces\StorageServiceInterface', [], [], '', false);
        }

        return $this->storageServiceMock;
    }

    /**
     * @param string $routerName
     * @return SecureFlowService
     */
    public function buildService($routerName)
    {
        return new SecureFlowService(
            $this->getRouterMock(),
            $routerName,
            $this->getEventDispatcherInterfaceMock(),
            $this->getApiPayInsMock(),
            $this->getStorageServiceInterfaceMock()
        );
    }

    public function testGetSecureFlowReturnUrl()
    {
        $this->getRouterMock()
            ->expects($this->once())
            ->method('generate')
            ->with('routerNameValue', [], Router::ABSOLUTE_URL)
            ->willReturn('http://foo.bar.com/return');

        $this->assertEquals(
            'http://foo.bar.com/return',
            $this->buildService('routerNameValue')->getSecureFlowReturnUrl()
        );
    }

    public function testPrepareSecureFlowError()
    {
        $payInMock = new PayIn();
        $payInMock->Id = 1234;
        $payInMock->Status = 'ERROR';

        $secureFlowSessionMock = $this->getMock('UniAlteri\MangoPayBundle\Entity\SecureFlowSession', [], [], '', false);
        $secureFlowSessionMock->expects($this->once())->method('setPayInId')->with(1234)->willReturnSelf();

        $responseMock = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $responseMock->expects($this->never())->method('setStatusCode');

        $service = $this->buildService('routerNameValue');
        $this->assertEquals($service, $service->prepareSecureFlow($payInMock, $secureFlowSessionMock, $responseMock));
    }

    public function testPrepareSecureFlowSuccess()
    {
        $payInMock = new PayIn();
        $payInMock->Id = 1234;
        $payInMock->Status = 'SUCCESSFULL';

        $secureFlowSessionMock = $this->getMock('UniAlteri\MangoPayBundle\Entity\SecureFlowSession', [], [], '', false);
        $secureFlowSessionMock->expects($this->once())->method('setPayInId')->with(1234)->willReturnSelf();

        $responseMock = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $responseMock->expects($this->never())->method('setStatusCode');

        $service = $this->buildService('routerNameValue');
        $this->assertEquals($service, $service->prepareSecureFlow($payInMock, $secureFlowSessionMock, $responseMock));
    }

    public function testPrepareSecureFlowCreatedWithoutDetail()
    {
        $payInMock = new PayIn();
        $payInMock->Id = 1234;
        $payInMock->Status = 'CREATED';

        $secureFlowSessionMock = $this->getMock('UniAlteri\MangoPayBundle\Entity\SecureFlowSession', [], [], '', false);
        $secureFlowSessionMock->expects($this->once())->method('setPayInId')->with(1234)->willReturnSelf();

        $responseMock = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $responseMock->expects($this->never())->method('setStatusCode');

        $service = $this->buildService('routerNameValue');
        $this->assertEquals($service, $service->prepareSecureFlow($payInMock, $secureFlowSessionMock, $responseMock));
    }

    public function testPrepareSecureFlowCreatedWithDetail()
    {
        $payInDetail = new PayInExecutionDetailsDirect();
        $payInDetail->SecureModeRedirectURL = 'https://3d.secure.com/url';
        $payInMock = new PayIn();
        $payInMock->Id = 1234;
        $payInMock->Status = 'CREATED';
        $payInMock->ExecutionDetails = $payInDetail;

        $secureFlowSessionMock = $this->getMock('UniAlteri\MangoPayBundle\Entity\SecureFlowSession', [], [], '', false);
        $secureFlowSessionMock->expects($this->once())->method('setPayInId')->with(1234)->willReturnSelf();

        /**
         * @var Response|\PHPUnit_Framework_MockObject_MockObject $responseMock
         */
        $responseMock = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $responseMock->expects($this->once())->method('setStatusCode')->with(302)->willReturnSelf();

        $service = $this->buildService('routerNameValue');
        $this->assertEquals($service, $service->prepareSecureFlow($payInMock, $secureFlowSessionMock, $responseMock));
        $this->assertEquals($responseMock->headers->get('Location'), 'https://3d.secure.com/url');
    }

    /**
     * @expectedException \UniAlteri\MangoPayBundle\Exception\BadMangoReturnException
     */
    public function testProcessMangoPayReturnExceptionOnInvalidReturn()
    {
        /**
         * @var Response|\PHPUnit_Framework_MockObject_MockObject $responseMock
         */
        $responseMock = $this->getMock('Symfony\Component\HttpFoundation\Response');

        $this->getApiPayInsMock()
            ->expects($this->once())
            ->method('Get')
            ->with(1234)
            ->willReturn(null);

        $this->buildService('routerNameValue')->processMangoPayReturn(1234, $responseMock);
    }

    public function testProcessMangoPayReturnOnError()
    {
        /**
         * @var Response|\PHPUnit_Framework_MockObject_MockObject $responseMock
         */
        $responseMock = $this->getMock('Symfony\Component\HttpFoundation\Response');

        $payInMock = new PayIn();
        $payInMock->Id = 1234;
        $payInMock->Status = 'ERROR';

        $this->getApiPayInsMock()
            ->expects($this->once())
            ->method('Get')
            ->with(1234)
            ->willReturn($payInMock);

        $secureFlowSessionMock = $this->getMock('UniAlteri\MangoPayBundle\Entity\SecureFlowSession', [], [], '', false);
        $this->getStorageServiceInterfaceMock()
            ->expects($this->once())
            ->method('get')
            ->with('MANGO_SECURE_FLOW1234')
            ->willReturn($secureFlowSessionMock);

        $this->getEventDispatcherInterfaceMock()
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                MangoPayEvents::SECURE_FLOW_ERROR,
                new SecureFlowEvent($payInMock, $responseMock, $secureFlowSessionMock)
            );

        $this->buildService('routerNameValue')->processMangoPayReturn(1234, $responseMock);
    }

    public function testProcessMangoPayReturnOnSuccess()
    {
        /**
         * @var Response|\PHPUnit_Framework_MockObject_MockObject $responseMock
         */
        $responseMock = $this->getMock('Symfony\Component\HttpFoundation\Response');

        $payInMock = new PayIn();
        $payInMock->Id = 1234;
        $payInMock->Status = 'SUCCEEDED';

        $this->getApiPayInsMock()
            ->expects($this->once())
            ->method('Get')
            ->with(1234)
            ->willReturn($payInMock);

        $secureFlowSessionMock = $this->getMock('UniAlteri\MangoPayBundle\Entity\SecureFlowSession', [], [], '', false);
        $this->getStorageServiceInterfaceMock()
            ->expects($this->once())
            ->method('get')
            ->with('MANGO_SECURE_FLOW1234')
            ->willReturn($secureFlowSessionMock);

        $this->getEventDispatcherInterfaceMock()
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                MangoPayEvents::SECURE_FLOW_SUCCESS,
                new SecureFlowEvent($payInMock, $responseMock, $secureFlowSessionMock)
            );

        $this->buildService('routerNameValue')->processMangoPayReturn(1234, $responseMock);
    }
}