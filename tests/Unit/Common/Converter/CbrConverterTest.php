<?php

namespace App\Tests\Unit\Common\Converter;

use App\Common\ApiClient\CbrExchangeRateApiClient;
use App\Common\ApiClient\Dto\CurrencyRateDto;
use App\Common\ApiClient\Exception\RequestException;
use App\Common\ApiClient\ExchangeRateApiClientInterface;
use App\Common\Converter\CbrConverter;
use App\Common\Converter\ConverterInterface;
use App\Tests\Utils\UnitTestingUtils;
use DivisionByZeroError;
use PHPUnit\Framework\TestCase;

final class CbrConverterTest extends TestCase
{
    private ConverterInterface $converter;

    public function setUp(): void
    {
        parent::setUp();
        $this->converter = new CbrConverter();
    }

    public function testCreate()
    {
        $this->assertInstanceOf(CbrConverter::class, $this->converter);
    }

    public function testExtendsFromConverterInterface()
    {
        $this->assertInstanceOf(ConverterInterface::class, $this->converter);
    }

    public function testSuccessConvert()
    {
        $fromCurrencyRate = new CurrencyRateDto();
        $fromCurrencyRate->value = 10;
        $fromCurrencyRate->nominal = 1;
        $toCurrencyRate = new CurrencyRateDto();
        $toCurrencyRate->value = 20;
        $toCurrencyRate->nominal = 1;
        $converterResult = $this->converter->convert($fromCurrencyRate, $toCurrencyRate, 100);
        $this->assertIsFloat($converterResult);
        $this->assertEquals(50, $converterResult);
    }

    public function testConvertThrowDivisionByZeroErrorException()
    {
        $fromCurrencyRate = new CurrencyRateDto();
        $fromCurrencyRate->value = 10;
        $fromCurrencyRate->nominal = 0;
        $toCurrencyRate = new CurrencyRateDto();
        $toCurrencyRate->value = 20;
        $toCurrencyRate->nominal = 1;
        $this->expectException(DivisionByZeroError::class);
        $this->converter->convert($fromCurrencyRate, $toCurrencyRate, 100);
    }
}