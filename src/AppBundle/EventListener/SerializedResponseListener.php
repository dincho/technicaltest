<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Serializer\SerializerInterface;

class SerializedResponseListener
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (!$event->getRequest()->attributes->has('_format')) {
            return;
        }

        $format = $event->getRequest()->attributes->get('_format');
        $contentType = $this->getContentType($format);
        $serializedData = $this->serializer->serialize(
            $event->getControllerResult(),
            $format
        );

        $event->setResponse(new Response(
            $serializedData,
            Response::HTTP_OK,
            ['Content-Type' => $contentType]
        ));
    }

    /**
     * @param  string $format
     * @return string
     * @throws \InvalidArgumentException If format is not supported
     */
    private function getContentType($format)
    {
        if ('json' === $format) {
            return 'application/json';
        }

        throw new \InvalidArgumentException('Unsupported format');
    }
}
