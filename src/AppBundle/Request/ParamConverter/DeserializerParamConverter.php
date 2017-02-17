<?php

namespace AppBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class DeserializerParamConverter implements ParamConverterInterface
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

    /**
     * {@inheritdoc}
     *
     * @throws NotFoundHttpException When doctor is not found
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $param = $configuration->getName();

        if (!$request->attributes->has('_format')) {
            return false;
        }

        $value = $request->get($param);

        if (!$value) {
            throw new BadRequestHttpException('Object is required');   
        }

        $format = $request->attributes->get('_format');
        $object = $this->serializer->deserialize($value, $configuration->getClass(), $format);

        $request->attributes->set($param, $object);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        $options = $configuration->getOptions();
        if (empty($options['serialized'])) {
            return false;
        }

        return true;
    }
}
