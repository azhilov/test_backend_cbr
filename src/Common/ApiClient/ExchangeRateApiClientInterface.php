<?php

namespace App\Common\ApiClient;

interface ExchangeRateApiClientInterface
{
    public function request(): array;
}
