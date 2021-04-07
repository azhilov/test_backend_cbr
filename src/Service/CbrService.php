<?php

namespace App\Service;

use App\Common\ApiClient\Dto\CurrencyRateDto;
use App\Common\ApiClient\ExchangeRateApiClientInterface;
use App\Common\Converter\ConverterInterface;
use InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CbrService
{
    public function __construct(
        private ExchangeRateApiClientInterface $apiClient,
        private CacheInterface $cache,
        private ConverterInterface $converter,
    ) {}

    public function convert(string $fromCurrency, string $toCurrency, float $value): float
    {
        $rateForFromCurrency = $this->getExchangeRate($fromCurrency);
        $rateForToCurrency = $this->getExchangeRate($toCurrency);

        return $this->converter->convert($rateForFromCurrency, $rateForToCurrency, $value);
    }

    private function getExchangeRate(string $currencyCharCode): CurrencyRateDto
    {
        $exchangeRates = $this->cache->get('exchange-rates', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            return $this->apiClient->request();
        });

        if (!key_exists($currencyCharCode, $exchangeRates)) {
            throw new InvalidArgumentException("Курс обмена для валюты с кодом $currencyCharCode неизвестен.");
        }

        return $exchangeRates[$currencyCharCode];
    }
}
