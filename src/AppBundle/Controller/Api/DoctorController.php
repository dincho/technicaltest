<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Doctor;
use AppBundle\Entity\Patient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ValidatorInterface;

class DoctorController
{
    /** @var Validator */
    private $validator;

    /**
     * @param Validator $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @ParamConverter("doctor")
     * @ParamConverter("patient", options={"serialized": true})
     *
     * @return array
     */
    public function createPatientAction(Doctor $doctor, Patient $patient)
    {
        $errors = $this->validator->validate($patient);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $doctor->addPatient($patient);
        //TODO: persist to storage layer

        return [
            'doctor' => $doctor,
            'msg' => 'Here are the doctor info and their patients',
        ];
    }
}
