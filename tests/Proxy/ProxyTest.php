<?php
namespace Proxy;

use PHPUnit_Framework_TestCase;
use Proxy\Exception\UnexpectedValueException;
use Proxy\Adapter\Dummy\DummyAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProxyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Proxy
     */
    private $proxy;

    public function setUp()
    {
        $this->proxy = new Proxy(new DummyAdapter());
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     */
    public function to_throws_exception_if_no_request_is_given()
    {
        $this->proxy->to('/');
    }


    /**
     * @test
     */
    public function to_returns_symfony_response()
    {
        $response = $this->proxy->forward(Request::createFromGlobals())->to('/');

        $this->assertTrue($response instanceof Response);
    }

    /**
     * @test
     */
    public function to_applies_request_filters()
    {
        $filter = $this->getMockBuilder('\Proxy\Request\Filter\RequestFilter')
            ->getMock();

        $filter->expects($this->once())
            ->method('filter');

        $this->proxy->addRequestFilter($filter);

        $this->proxy->forward(Request::createFromGlobals())->to('/');
    }

    /**
     * @test
     */
    public function to_applies_response_filters()
    {
        $filter = $this->getMockBuilder('\Proxy\Response\Filter\ResponseFilter')
            ->getMock();

        $filter->expects($this->once())
            ->method('filter');

        $this->proxy->addResponseFilter($filter);

        $this->proxy->forward(Request::createFromGlobals())->to('/');
    }

    /**
     * @test
     */
    public function to_sends_request()
    {
        $request = Request::createFromGlobals();
        $url = 'http://www.rebuy.de';

        $adapter = $this->getMockBuilder('\Proxy\Adapter\Dummy\DummyAdapter')
            ->getMock();

        $adapter->expects($this->once())
            ->method('send')
            ->with($request, $url)
            ->willReturn(Response::create());

        $proxy = new Proxy($adapter);
        $proxy->forward($request)->to($url);

    }

}
