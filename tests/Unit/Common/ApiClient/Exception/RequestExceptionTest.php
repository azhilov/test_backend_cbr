<?php

namespace App\Tests\Unit\Common\ApiClient\Exception;

use App\Common\ApiClient\Exception\RequestException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class RequestExceptionTest extends TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf(RequestException::class, new RequestException());
    }

    public function testExtendsFromRuntimeException()
    {
        $this->assertInstanceOf(RuntimeException::class, new RequestException());
    }
}