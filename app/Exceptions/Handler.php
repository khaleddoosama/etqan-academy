<?php

namespace App\Exceptions;

use App\Http\Controllers\Api\ApiResponseTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PDOException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Yoeunes\Toastr\Facades\Toastr;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as SymfonyNotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\LengthRequiredHttpException;
use Symfony\Component\HttpKernel\Exception\LockedHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionRequiredHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            Log::error($e->getMessage());
        });
    }

    public function render($request, Throwable $exception)
    {
         // log message
         Log::error($exception->getMessage());
         // log trace
         Log::error($exception->getTraceAsString());
         // log file
         Log::error($exception->getFile());
         // log line
         Log::error($exception->getLine());

        if ($request->wantsJson() || $request->is('api/*')) {  // Check if the request is for API
            return $this->handleApiException($request, $exception);
        } else {
            return $this->handleWebException($request, $exception);
        }
    }

    protected function handleApiException($request, Throwable $exception)
    {
        // Map exception types to HTTP status codes
        $exceptionTypeToStatus = [
            // 4xx Client Errors
            ModelNotFoundException::class => 404,
            NotFoundResourceException::class => 404,
            NotFoundHttpException::class => 404,
            AuthorizationException::class => 403,
            ValidationException::class => 422,
            AuthenticationException::class => 401,
            MethodNotAllowedHttpException::class => 405,
            HttpResponseException::class => 400,
            BadRequestHttpException::class => 400,
            AccessDeniedHttpException::class => 403,
            HttpException::class => 400,
            ConflictHttpException::class => 409,
            GoneHttpException::class => 410,
            UnsupportedMediaTypeHttpException::class => 415,
            UnprocessableEntityHttpException::class => 422,
            TooManyRequestsHttpException::class => 429,
            ServiceUnavailableHttpException::class => 503,
            PreconditionFailedHttpException::class => 412,
            PreconditionRequiredHttpException::class => 428,
            UnauthorizedHttpException::class => 401,
            LengthRequiredHttpException::class => 411,

            // 5xx Server Errors
            \ErrorException::class => 500,
            \RuntimeException::class => 500,
            \Exception::class => 500,
            QueryException::class => 500,
            PDOException::class => 500,
            HttpException::class => 500,

            // Laravel Specific Exceptions
            \Illuminate\Session\TokenMismatchException::class => 419, // CSRF token mismatch
            \Illuminate\Contracts\Filesystem\FileNotFoundException::class => 404,

            // Additional Symfony HTTP exceptions
            LockedHttpException::class => 423,
            PreconditionFailedHttpException::class => 412,

            // General PHP Exceptions
            \LogicException::class => 500,
            \DomainException::class => 500,
            \InvalidArgumentException::class => 400,
            \BadFunctionCallException::class => 500,
            \BadMethodCallException::class => 500,
            \OutOfRangeException::class => 500,
            \UnderflowException::class => 500,
            \OverflowException::class => 500,
            \UnexpectedValueException::class => 500,
        ];

        foreach ($exceptionTypeToStatus as $type => $status) {
            if ($exception instanceof $type) {
                return $this->apiResponse(null, $exception->getMessage(), $status);
            }
        }
        Log::error('Unknown exception: ' . $exception->getMessage());
        // Default response for other types of exceptions
        return $this->apiResponse(null, $exception->getMessage(), 499);
    }

    protected function handleWebException($request, Throwable $exception)
    {


        if ($exception instanceof ValidationException) {
            // Handle validation exceptions
            Toastr::error($exception->getMessage(), __('status.error_in_inputs'));
        } elseif ($exception instanceof AuthorizationException) {
            // Handle authorization exceptions
            Toastr::error($exception->getMessage(), __('status.error_in_permissions'));
        } elseif ($exception instanceof AuthenticationException) {
            // Handle authentication exceptions
            Toastr::error($exception->getMessage(), __('status.error_in_register'));
        } elseif ($exception instanceof PDOException) {
            // Handle query exceptions
            Toastr::error($exception->getMessage(), __('status.database_error'));
            return redirect()->back();
        } else {
            // Handle other exceptions
            Toastr::error($exception->getMessage(),  __('status.error'));
        }

        return parent::render($request, $exception);
    }
}
