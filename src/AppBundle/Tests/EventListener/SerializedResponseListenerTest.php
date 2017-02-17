<?php

namespace AppBundle\Tests\EventListener;

use AppBundle\Entity\Patient;
use AppBundle\EventListener\SerializedResponseListener;

use PHPUnit\Framework\TestCase;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SerializedResponseListenerTest extends TestCase
{
    public function testSerializeResponse()
    {
        $event = $this->getEvent('json');
        $listener = new SerializedResponseListener($this->getSerializer(json_encode('test')));

        $listener->onKernelView($event);

        $this->assertEquals(json_encode('test'), $event->getResponse()->getContent());
    }

    public function testDoesNotSerializeWithoutFormat()
    {
        $listener = new SerializedResponseListener($this->getSerializer());

        $listener->onKernelView($this->getEvent());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnUnsupportedFormat()
    {
        $listener = new SerializedResponseListener($this->getSerializer());

        $listener->onKernelView($this->getEvent('binary'));
    }

    /**
     * @param  string $serializedData JSON format
     * @return SerializerInterface
     */
    private function getSerializer($serializedData = null)
    {
        $serializer = $this->getMockBuilder(SerializerInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $serializer->expects($this->exactly(null === $serializedData ? 0 : 1))
             ->method('serialize')
             ->willReturn($serializedData);

        return $serializer;
    }

    /**
     * @param  string $format Serialization format
     * @return GetResponseForControllerResultEvent
     */
    private function getEvent($format = null)
    {
        $attributes = [];
        if ($format) {
            $attributes = ['_format' => $format];
        }

        $kernel = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $request = new Request([], [], $attributes);
        $event = new GetResponseForControllerResultEvent(
            $kernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Patient()
        );


        return $event;
    }
}
