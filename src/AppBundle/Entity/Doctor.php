<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Patient;

class Doctor
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Patient[]
     */
    private $patients;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Patient $patient
     */
    public function addPatient(Patient $patient)
    {
        $this->patients[] = $patient;
    }

    /**
     * @return Patient[]
     */
    public function getPatients()
    {
        return $this->patients;
    }
}
