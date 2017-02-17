<?php

namespace AppBundle\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DoctorControllerTest extends WebTestCase
{
    public function testShouldAddPatientWithValidData()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/doctor/1/patient',
            [
                'patient' => json_encode([
                    'name' => 'Ivan Dimitrov',
                    'dob' => '1980-05-01T13:00:59+00:00',
                    'gender' => 1, //male
                ])
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('doctor', $data);
        $this->assertArrayHasKey('patients', $data['doctor']);
        $this->assertCount(1, $data['doctor']['patients']);
    }

    public function testShouldFailWithInvalidData()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/doctor/1/patient',
            [
                'patient' => json_encode([
                    'gender' => 1, //male
                ])
            ]
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
    }
}
