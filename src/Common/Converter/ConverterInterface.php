<?php

namespace App\Common\Converter;

use App\Common\ApiClient\Dto\CurrencyRateDto;

interface ConverterInterface {
    public function convert(CurrencyRateDto $from, CurrencyRateDto $to, float $value): float;
}
