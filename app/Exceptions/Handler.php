<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception $e
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $e)
    {
        $rendered = parent::render($request, $e);
        $statusCode = $rendered->getStatusCode();

        // For http errors 422 throwed by the Laravel validator
        if ($e instanceof ValidationException) {
            return response()->json($e->validator->errors(), $statusCode);
        }

        // The default error structure
        $response = [
            'error_code' => 'server_error',
            'message' => 'Something went wrong',
        ];

        // For http exceptions use the exception message
        if ($e instanceof HttpException) {
            $response['error_code'] = 'general_error';
            $response['message'] = $e->getMessage() ?: class_basename($e);
            $response['code'] = $e->getCode();
        }

        // 400: For http exceptions use the exception message
        if ($e instanceof BadRequestHttpException) {
            $response['error_code'] = 'bad_request';
            $response['message'] = $e->getMessage();
            $response['code'] = $e->getCode();
        }

        // 401: For http exceptions use the exception message
        if ($e instanceof UnauthorizedHttpException) {
            $response['error_code'] = 'unauthorized';
            $response['message'] = $e->getMessage();
            $response['code'] = $e->getCode();
        }

        // 404: For http exceptions use the exception message
        if ($e instanceof NotFoundHttpException) {
            $response['error_code'] = 'not_found';
            if (empty($response['message'])) {
                $response['message'] = $e->getMessage();
            }
        }

        // Add debug info to the response
        if (env('APP_DEBUG', false) && $response['error_code'] == "server_error")
        {
            $fe = FlattenException::create($e);
            $response['debug'] = [
                'message' => $e->getMessage(),
                'trace' => $fe->toArray()
            ];
        }

        $headers = [];
        if (method_exists($e, 'getHeaders')) {
            $headers = $e->getHeaders();
        }

        return response()->json($response, $statusCode)->withHeaders($headers);
    }
}
