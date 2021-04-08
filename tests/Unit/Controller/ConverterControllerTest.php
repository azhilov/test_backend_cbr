<?php

namespace App\Tests\Unit\Controller;

use App\Service\CbrService;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Controller\ConverterController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ConverterControllerTest extends TestCase
{
    private ConverterController $controllerWithoutValidatorErrors;
    private ConverterController $controllerWithValidatorErrors;
    private CbrService $cbrServiceStub;
    private Request $requestStub;

    public function setUp(): void
    {
        parent::setUp();
        $this->cbrServiceStub = $this->createMock(CbrService::class);

        $validatorStubWithoutErrors = $this->createMock(ValidatorInterface::class);
        $validatorStubWithoutErrors->method('validate')->willReturn([]);

        $validatorStubWithErrors = $this->createMock(ValidatorInterface::class);
        $validatorStubWithErrors->method('validate')->willReturn(['error1', 'error2', 'error3',]);

        $container = new Container();

        $this->controllerWithoutValidatorErrors = new ConverterController($validatorStubWithoutErrors);
        $this->controllerWithoutValidatorErrors->setContainer($container);

        $this->controllerWithValidatorErrors = new ConverterController($validatorStubWithErrors);
        $this->controllerWithValidatorErrors->setContainer($container);

        $this->requestStub = $this->createMock(Request::class);
        $this->requestStub->method('get')->willReturnCallback(
            fn($paramName) => match ($paramName) {
                'from' => 'RUB',
                'to' => 'SEK',
                'value' => 1000,
            }
        );
    }

    public function testCreate()
    {
        $this->assertInstanceOf(ConverterController::class, $this->controllerWithoutValidatorErrors);
    }

    public function testSuccessConvert()
    {
        $this->cbrServiceStub->method('convert')->willReturn(100.0);
        $actionResult = $this->controllerWithoutValidatorErrors->convert($this->requestStub, $this->cbrServiceStub);
        $this->assertInstanceOf(JsonResponse::class, $actionResult);
        $this->assertEquals(['value' => 100], json_decode($actionResult->getContent(), true));
        $this->assertEquals(200, $actionResult->getStatusCode());
    }

    public function testConvertWitHasValidationErrors()
    {
        $this->cbrServiceStub->method('convert')->willReturn(100.0);
        $actionResult = $this->controllerWithValidatorErrors->convert($this->requestStub, $this->cbrServiceStub);
        $this->assertInstanceOf(JsonResponse::class, $actionResult);
        $this->assertEquals(
            ['message' => 'Некорректно задан один из параметров запроса.'],
            json_decode($actionResult->getContent(), true)
        );
        $this->assertEquals(400, $actionResult->getStatusCode());
    }

    public function testConvertThrowInvalidArgumentException()
    {
        $this->cbrServiceStub->method('convert')
            ->willThrowException(new InvalidArgumentException('InvalidArgumentException'));
        $actionResult = $this->controllerWithoutValidatorErrors->convert($this->requestStub, $this->cbrServiceStub);
        $this->assertInstanceOf(JsonResponse::class, $actionResult);
        $this->assertEquals(
            ['message' => 'InvalidArgumentException'],
            json_decode($actionResult->getContent(), true)
        );
        $this->assertEquals(400, $actionResult->getStatusCode());
    }

    public function testConvertThrowException()
    {
        $this->cbrServiceStub->method('convert')
            ->willThrowException(new Exception('Exception'));
        $actionResult = $this->controllerWithoutValidatorErrors->convert($this->requestStub, $this->cbrServiceStub);
        $this->assertInstanceOf(JsonResponse::class, $actionResult);
        $this->assertEquals(
            ['message' => 'Exception'],
            json_decode($actionResult->getContent(), true)
        );
        $this->assertEquals(500, $actionResult->getStatusCode());
    }
}