<?php

namespace App\Common\Converter;

use App\Common\ApiClient\Dto\CurrencyRateDto;
use DivisionByZeroError;

final class CbrConverter implements ConverterInterface
{
    public function convert(CurrencyRateDto $from, CurrencyRateDto $to, float $value): float
    {
        if ($from->nominal === 0 || $to->nominal === 0) {
            throw new DivisionByZeroError("Номиналы валют из курса обмена не могут быть нулевыми.");
        }

        $byOneForFromCurrency = $from->value / $from->nominal;
        $byOneForToCurrency = $to->value / $to->nominal;

        return $value * $byOneForFromCurrency / $byOneForToCurrency;
    }

}