<?php

namespace AppBundle\Request\ParamConverter;

use AppBundle\Entity\Doctor;
use AppBundle\Repository\DoctorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DoctorParamConverter implements ParamConverterInterface
{
    /** @var DoctorRepository */
    private $doctorRepository;

    /**
     * @param DoctorRepository $doctorRepository
     */
    public function __construct(DoctorRepository $doctorRepository)
    {
        $this->doctorRepository = $doctorRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotFoundHttpException When doctor is not found
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $param = $configuration->getName();

        if (!$request->attributes->has($param)) {
            return false;
        }

        $value = $request->attributes->get($param);

        if (!$value && $configuration->isOptional()) {
            return false;
        }

        if (null === $doctor = $this->doctorRepository->selectById($value)) {
            throw new NotFoundHttpException('Doctor not found');
        }

        $request->attributes->set($param, $doctor);

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

        return Doctor::class === $configuration->getClass();
    }
}
