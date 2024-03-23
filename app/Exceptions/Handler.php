<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use PDOException;
use Yoeunes\Toastr\Facades\Toastr;

class Handler extends ExceptionHandler
{
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
            //
        });
    }

    public function render($request, Throwable $exception)
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
