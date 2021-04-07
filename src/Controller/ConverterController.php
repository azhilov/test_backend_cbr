<?php

namespace App\Controller;

use App\Service\CbrService;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ConverterController extends AbstractController
{
    public function __construct(private ValidatorInterface $validator)
    {}

    /**
     * @Route("/api/convert", name="convert_currency", methods={"POST"})
     *
     * @param Request $request
     * @param CbrService $cbrService Сервис ЦБРФ.
     *
     * @return Response
     */
    public function number(Request $request, CbrService $cbrService): Response
    {
        $fromCurrency = $request->get('from');
        $toCurrency = $request->get('to');
        $value = floatval($request->get('value'));
        $constraint = new Assert\Collection([
            'from' => new Assert\NotBlank(),
            'to' => new Assert\NotBlank(),
            'value' => new Assert\Positive(),
        ]);

        $validationErrors = $this->validator->validate(
            ['from' => $fromCurrency, 'to' => $toCurrency, 'value' => $value],
            $constraint
        );

        if (count($validationErrors) > 0) {
            return $this->json(
                ['message' => 'Некорректно задан один из параметров запроса.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $convertedValue = $cbrService->convert($fromCurrency, $toCurrency, $value);
        } catch (InvalidArgumentException $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['value' => $convertedValue]);
    }
}