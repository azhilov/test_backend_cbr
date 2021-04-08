<?php

namespace App\Tests\Unit\Service;

use App\Common\ApiClient\CbrExchangeRateApiClient;
use App\Common\ApiClient\Dto\CurrencyRateDto;
use App\Common\Converter\CbrConverter;
use App\Service\CbrService;
use App\Tests\Utils\UnitTestingUtils;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

final class CbrServiceTest extends TestCase
{
    private CbrService $service;

    public function setUp(): void
    {
        parent::setUp();
        $apiClient = new CbrExchangeRateApiClient($_ENV['EXCHANGE_RATE_API_URL']);
        $converter = new CbrConverter();
        $currencyRates = $apiClient->request();
        $cacheStub = $this->createMock(CacheInterface::class);
        $cacheStub->method('get')->willReturn($currencyRates);
        $this->service = new CbrService($apiClient, $cacheStub, $converter);
    }

    public function testCreate()
    {
        $this->assertInstanceOf(CbrService::class, $this->service);
    }

    public function testSuccessGetExchangeRate()
    {
        $methodResult = UnitTestingUtils::callMethod(
            $this->service,
            'getExchangeRate',
            ['currencyCharCode' => 'BRL']
        );

        $this->assertInstanceOf(CurrencyRateDto::class, $methodResult);
        $this->assertEquals('R01115', $methodResult->id);
        $this->assertEquals(986, $methodResult->numCode);
        $this->assertEquals('BRL', $methodResult->charCode);
    }

    public function testGetExchangeRateThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        UnitTestingUtils::callMethod(
            $this->service,
            'getExchangeRate',
            ['currencyCharCode' => 'TTT']
        );
    }

    public function testSuccessConvert()
    {
        $converterResult = $this->service->convert('CHF', 'SEK', 100);
        $this->assertIsFloat($converterResult);
        $this->assertEquals(926.35889724644, $converterResult);
    }
}