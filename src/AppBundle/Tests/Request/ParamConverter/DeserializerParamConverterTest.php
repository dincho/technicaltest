<?php

namespace AppBundle\Tests\Request\ParamConverter;

use AppBundle\Request\ParamConverter\DeserializerParamConverter;

use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class DeserializerParamConverterTest extends TestCase
{
    public function testDoesNotSupportNullClass()
    {
        $converter = new DeserializerParamConverter($this->getSerializer());
        $config = new ParamConverter(['class' => null]);

        $this->assertFalse($converter->supports($config));
    }

    public function testNotSupportWithFalsySerializedOption()
    {
        $converter = new DeserializerParamConverter($this->getSerializer());
        $config = new ParamConverter([
            'class' => \StdClass::class,
            'options' => ['serialized' => false],
        ]);

        $this->assertFalse($converter->supports($config));
    }

    public function testSerializeOnlyWithSerializedOptionOn()
    {
        $converter = new DeserializerParamConverter($this->getSerializer());
        $config = new ParamConverter([
            'class' => \StdClass::class,
            'options' => ['serialized' => true],
        ]);

        $this->assertTrue($converter->supports($config));
    }

    public function testDoesNotApplyWithoutFormat()
    {
        $converter = new DeserializerParamConverter($this->getSerializer());
        $config = new ParamConverter([
            'class' => \StdClass::class,
            'name' => 'obj',
            'options' => ['serialized' => true],
        ]);

        $object = new \StdClass();
        $request = $this->getRequest(null, ['obj' => $object]);

        $this->assertFalse($converter->apply($request, $config));
    }

    public function testShouldApplyOnDeserializedObject()
    {
        $object = new \StdClass();
        $converter = new DeserializerParamConverter($this->getSerializer($object));
        $config = new ParamConverter([
            'class' => \StdClass::class,
            'name' => 'obj',
            'options' => ['serialized' => true],
        ]);

        $request = $this->getRequest('json', ['obj' => $object]);

        $this->assertTrue($converter->apply($request, $config));
    }

    /**
     * @param  string $data JSON format
     * @return SerializerInterface
     */
    private function getSerializer($data = null)
    {
        $serializer = $this->getMockBuilder(SerializerInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $serializer->expects($this->exactly(null === $data ? 0 : 1))
             ->method('deserialize')
             ->willReturn($data);

        return $serializer;
    }

    /**
     * @param  string $format Serialization format
     * @param  array $data
     * @return Request
     */
    private function getRequest($format = null, array $data)
    {
        $attributes = [];
        if ($format) {
            $attributes = ['_format' => $format];
        }

        $kernel = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $request = new Request([], $data, $attributes);

        return $request;
    }
}
