<?php

namespace App\Common\ApiClient;

abstract class ExchangeRateApiClient implements ExchangeRateApiClientInterface
{
    public function __construct(protected string $apiUrl) {}
}
