<?php

namespace App\Tests\Unit\Common\ApiClient\Dto;

use App\Common\ApiClient\Dto\CurrencyRateDto;
use PHPUnit\Framework\TestCase;

final class CurrencyRateDtoTest extends TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf(CurrencyRateDto::class, new CurrencyRateDto());
    }
}