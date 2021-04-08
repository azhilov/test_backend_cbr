<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConvertControllerTest extends WebTestCase
{
    public function testSuccessConvert()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/convert',
            ['from' => 'AUD', 'to' => 'BGN', 'value' => 10]
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            ['value' => 12.571694974517637],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testConvertReturnBadParams()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/convert',
            ['to' => 'BGN', 'value' => 10]
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            ['message' => 'Некорректно задан один из параметров запроса.'],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testConvertReturnBadCurrencyCharCode()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/convert',
            ['from' => 'TTT', 'to' => 'BGN', 'value' => 10]
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            ['message' => 'Курс обмена для валюты с кодом TTT неизвестен.'],
            json_decode($client->getResponse()->getContent(), true)
        );
    }
}