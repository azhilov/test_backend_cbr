<?php

namespace App\Tests\Unit\Common\ApiClient;

use App\Common\ApiClient\CbrExchangeRateApiClient;
use App\Common\ApiClient\Dto\CurrencyRateDto;
use App\Common\ApiClient\Exception\RequestException;
use App\Common\ApiClient\ExchangeRateApiClientInterface;
use App\Tests\Utils\UnitTestingUtils;
use PHPUnit\Framework\TestCase;

final class CbrExchangeRateApiClientTest extends TestCase
{
    private ExchangeRateApiClientInterface $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = new CbrExchangeRateApiClient($_ENV['EXCHANGE_RATE_API_URL']);
    }

    public function testCreate()
    {
        $this->assertInstanceOf(CbrExchangeRateApiClient::class, $this->client);
    }

    public function testExtendsFromExchangeRateApiClientInterface()
    {
        $this->assertInstanceOf(ExchangeRateApiClientInterface::class, $this->client);
    }

    public function testSuccessRequest()
    {
        $response = $this->client->request();
        $this->assertIsArray($response);

        if (count($response) > 0) {
            $firstItem = array_shift($response);
            $this->assertInstanceOf(CurrencyRateDto::class, $firstItem);
        }
    }

    public function testRequestThrowRequestException()
    {
        $badClient = new CbrExchangeRateApiClient('bad_url');
        $this->expectException(RequestException::class);
        $badClient->request();
    }

    public function testParseResponse()
    {
        $response = simplexml_load_file(
            filename: $_ENV['EXCHANGE_RATE_API_URL'],
            options: LIBXML_COMPACT | LIBXML_NOBLANKS
        );
        $methodResult = UnitTestingUtils::callMethod(
            $this->client,
            'parseResponse',
            ['response' => $response]
        );

        $this->assertIsArray($methodResult);

        if (count($methodResult) > 0) {
            $firstItem = array_shift($methodResult);
            $this->assertInstanceOf(CurrencyRateDto::class, $firstItem);
        }
    }
}