<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Resources\Api\V1\ErrorResource;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler
{
    /**
     * Handle exceptions and return an appropriate JSON response.
     *
     * @param  Throwable  $exception  The thrown exception.
     */
    public function handles(Throwable $exception): ErrorResource
    {
        if ($exception instanceof QueryException || $exception instanceof InvalidFilterQuery) {
            return new ErrorResource([
                'errors' => [
                    [
                        'title' => 'Malformed or Invalid Query Parameters',
                        'detail' => 'Ensure that your query parameters are correctly formatted and valid.',
                    ],
                ],
            ], 400);
        }

        if ($exception instanceof TokenAbilitiesException) {
            return new ErrorResource([
                'errors' => [
                    [
                        'title' => 'Operation Not Allowed',
                        'detail' => 'You are not allowed to perform this operation.',
                    ],
                ],
            ], $exception->getCode());
        }

        if ($exception instanceof NotFoundHttpException) {
            return new ErrorResource([
                'errors' => [
                    [
                        'title' => 'Resource Not Found',
                        'detail' => 'The requested endpoint does not exist.',
                    ],
                ],
            ], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return new ErrorResource([
                'errors' => [
                    [
                        'title' => 'Method Not Allowed',
                        'detail' => 'The HTTP method used is not supported for this endpoint.',
                    ],
                ],
            ], 405);
        }

        if ($exception instanceof ValidationException) {
            $exceptionMessageBag = $exception->validator->getMessageBag();
            $formattedErrors = [];

            foreach ($exceptionMessageBag->getMessages() as $messages) {
                foreach ($messages as $message) {
                    $formattedErrors[] = [
                        'title' => 'Validation Error.',
                        'detail' => $message,
                    ];
                }
            }

            return new ErrorResource([
                'errors' => $formattedErrors,
            ], 422);
        }

        return new ErrorResource([
            'errors' => [
                [
                    'title' => 'Internal Server Error',
                    'detail' => 'An unexpected error occurred, please try later.',
                ],
            ],
        ], 500);
    }
}
