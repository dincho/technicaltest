<?php

/** 
 * Backport (copy-paste) from Symfony 3.1
 * http://symfony.com/blog/new-in-symfony-3-1-datetime-normalizer
 * https://github.com/symfony/symfony/pull/17411
 */

namespace AppBundle\Serializer\Normalizer;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes an object implementing the {@see \DateTimeInterface} to a date string.
 * Denormalizes a date string to an instance of {@see \DateTime} or {@see \DateTimeImmutable}.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class DateTimeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    const FORMAT_KEY = 'datetime_format';

    /**
     * @var string
     */
    private $format;

    /**
     * @param string $format
     */
    public function __construct($format = \DateTime::RFC3339)
    {
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if (!$object instanceof \DateTimeInterface) {
            throw new InvalidArgumentException('The object must implement the "\DateTimeInterface".');
        }

        $format = isset($context[self::FORMAT_KEY]) ? $context[self::FORMAT_KEY] : $this->format;

        return $object->format($format);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \DateTimeInterface;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnexpectedValueException
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        try {
            return \DateTime::class === $class ? new \DateTime($data) : new \DateTimeImmutable($data);
        } catch (\Exception $e) {
            throw new UnexpectedValueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        $supportedTypes = array(
            \DateTimeInterface::class => true,
            \DateTimeImmutable::class => true,
            \DateTime::class => true,
        );

        return isset($supportedTypes[$type]);
    }
}