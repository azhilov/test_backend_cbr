<?php

namespace App\Common\ApiClient\Dto;

final class CurrencyRateDto {
    public string $id;
    public string $numCode;
    public string $charCode;
    public int $nominal;
    public string $name;
    public float $value;
}
