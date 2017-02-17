<?php

namespace AppBundle\Tests\Serializer\Normalizer;

use AppBundle\Serializer\Normalizer\DateTimeNormalizer;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class DateTimeNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateTimeNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->normalizer = new DateTimeNormalizer();
    }

    public function testSupportNormalization()
    {
        $this->assertTrue($this->normalizer->supportsNormalization(new \DateTime()));
        $this->assertTrue($this->normalizer->supportsNormalization(new \DateTimeImmutable()));
        $this->assertFalse($this->normalizer->supportsNormalization(new \stdClass()));
    }

    public function testNormalize()
    {
        $this->assertEquals('2016-01-01T00:00:00+00:00', $this->normalizer->normalize(new \DateTime('2016/01/01')));
        $this->assertEquals('2016-01-01T00:00:00+00:00', $this->normalizer->normalize(new \DateTimeImmutable('2016/01/01')));
    }

    public function testContextFormat()
    {
        $this->assertEquals('2016', $this->normalizer->normalize(new \DateTime('2016/01/01'), null, array(DateTimeNormalizer::FORMAT_KEY => 'Y')));
    }

    public function testConstructorFormat()
    {
        $this->assertEquals('16', (new DateTimeNormalizer('y'))->normalize(new \DateTime('2016/01/01')));
    }

    /**
     * @expectedException \Symfony\Component\Serializer\Exception\InvalidArgumentException
     * @expectedExceptionMessage The object must implement the "\DateTimeInterface".
     */
    public function testInvalidDataThrowException()
    {
        $this->normalizer->normalize(new \stdClass());
    }

    public function testSupportDenormalization()
    {
        $this->assertTrue($this->normalizer->supportsDenormalization('2016-01-01T00:00:00+00:00', \DateTimeInterface::class));
        $this->assertTrue($this->normalizer->supportsDenormalization('2016-01-01T00:00:00+00:00', \DateTime::class));
        $this->assertTrue($this->normalizer->supportsDenormalization('2016-01-01T00:00:00+00:00', \DateTimeImmutable::class));
        $this->assertFalse($this->normalizer->supportsDenormalization('foo', 'Bar'));
    }

    public function testDenormalize()
    {
        $this->assertEquals(new \DateTimeImmutable('2016/01/01'), $this->normalizer->denormalize('2016-01-01T00:00:00+00:00', \DateTimeInterface::class));
        $this->assertEquals(new \DateTimeImmutable('2016/01/01'), $this->normalizer->denormalize('2016-01-01T00:00:00+00:00', \DateTimeImmutable::class));
        $this->assertEquals(new \DateTime('2016/01/01'), $this->normalizer->denormalize('2016-01-01T00:00:00+00:00', \DateTime::class));
    }

    /**
     * @expectedException \Symfony\Component\Serializer\Exception\UnexpectedValueException
     */
    public function testInvalidDateThrowException()
    {
        $this->normalizer->denormalize('invalid date', \DateTimeInterface::class);
    }
}