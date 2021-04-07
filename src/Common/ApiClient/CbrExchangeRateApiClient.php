<?php

namespace App\Common\ApiClient;

use App\Common\ApiClient\Dto\CurrencyRateDto;
use App\Common\ApiClient\Exception\RequestException;
use SimpleXMLElement;

final class CbrExchangeRateApiClient extends ExchangeRateApiClient
{
    public function request(): array
    {
        libxml_use_internal_errors(true);
        $response = simplexml_load_file(filename: $this->apiUrl, options: LIBXML_COMPACT | LIBXML_NOBLANKS);

        if (!$response) {
            throw new RequestException("Не удалось загрузить данные курсов обмена.");
        }

        return $this->parseResponse($response);
    }

    private function parseResponse(SimpleXMLElement $response): array
    {
        $exchangeRates = [];

        foreach ($response as $currency) {
            $currencyRate = new CurrencyRateDto();
            $currencyRate->id = strval($currency['ID']);
            $currencyRate->charCode = $currency->CharCode;
            $currencyRate->name = $currency->Name;
            $currencyRate->nominal = intval($currency->Nominal);
            $currencyRate->numCode = $currency->NumCode;
            $currencyRate->value = floatval(str_replace(',', '.', $currency->Value));
            $exchangeRates[$currencyRate->charCode] = $currencyRate;
        }

        return $exchangeRates;
    }
}
