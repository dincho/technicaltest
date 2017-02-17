<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Doctor;

class DoctorRepository implements RepositoryInterface
{
    /** @return Doctor */
    public function selectById($id)
    {
        // TODO: Implement selectById() method.
        $data = $this->getSampleData();
        if (isset($data[$id])) {
            return $data[$id];
        }

        return null;
    }

    /**
     * @return array Fake data
     */
    private function getSampleData()
    {
        $doctor1 = new Doctor();
        $doctor1->setId(1);
        $doctor1->setName('John Smith');

        $doctor2 = new Doctor();
        $doctor2->setId(2);
        $doctor2->setName('Will Clain');

        return [
            1 => $doctor1,
            2 => $doctor2 
        ];
    }
}
