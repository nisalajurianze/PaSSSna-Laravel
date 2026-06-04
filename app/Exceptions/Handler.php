<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\BusinessException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        \Illuminate\Auth\AuthenticationException::class => 'warning',
        \Illuminate\Validation\ValidationException::class => 'info',
        \Illuminate\Database\Eloquent\ModelNotFoundException::class => 'warning',
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => 'warning',
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class => 'warning',
        \Illuminate\Http\Exceptions\ThrottleRequestsException::class => 'warning',
        \Illuminate\Database\QueryException::class => 'error',
        \PDOException::class => 'critical',
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Validation\ValidationException::class,
        \App\Exceptions\BusinessException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'credit_card_number',
        'cvv',
        'expiry_date',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Send exception to external service like Sentry, Bugsnag, etc.
            if (app()->environment('production')) {
                $this->sendToMonitoringService($e);
            }
        });

        // Custom exception rendering for API requests
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->handleApiException($e, $request);
            }

            return null;
        });
    }

    /**
     * Handle API exceptions
     */
    private function handleApiException(Throwable $e, Request $request): JsonResponse
    {
        $statusCode = $this->getStatusCode($e);
        $response = [
            'success' => false,
            'message' => $this->getErrorMessage($e, $statusCode),
            'error_code' => $this->getErrorCode($e),
            'timestamp' => now()->toISOString(),
        ];

        // Add debug information in development
        if (config('app.debug')) {
            $response['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->map(function ($trace) {
                    return isset($trace['file']) ? $trace['file'] . ':' . $trace['line'] : null;
                })->filter()->toArray(),
            ];
        }

        // Add validation errors if it's a validation exception
        if ($e instanceof ValidationException) {
            $response['errors'] = $e->errors();
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Get HTTP status code from exception
     */
    private function getStatusCode(Throwable $e): int
    {
        return match (true) {
            $e instanceof AuthenticationException => 401,
            $e instanceof ValidationException => 422,
            $e instanceof BusinessException => 422,
            $e instanceof ModelNotFoundException => 404,
            $e instanceof NotFoundHttpException => 404,
            $e instanceof MethodNotAllowedHttpException => 405,
            $e instanceof ThrottleRequestsException => 429,
            $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException => $e->getStatusCode(),
            default => 500
        };
    }

    /**
     * Get user-friendly error message
     */
    private function getErrorMessage(Throwable $e, int $statusCode): string
    {
        if (config('app.debug')) {
            return $e->getMessage();
        }

        if ($e instanceof BusinessException) {
            return 'Business logic error.';
        }

        return match ($statusCode) {
            401 => 'Unauthorized. Please login to continue.',
            403 => 'Forbidden. You do not have permission to access this resource.',
            404 => 'Resource not found.',
            405 => 'Method not allowed.',
            422 => 'The given data was invalid.',
            429 => 'Too many requests. Please try again later.',
            500 => 'Internal server error. Please try again later.',
            503 => 'Service unavailable. Please try again later.',
            default => 'An error occurred. Please try again.',
        };
    }

    /**
     * Get error code from exception
     */
    private function getErrorCode(Throwable $e): string
    {
        return match (true) {
            $e instanceof AuthenticationException => 'UNAUTHENTICATED',
            $e instanceof ValidationException => 'VALIDATION_ERROR',
            $e instanceof BusinessException => 'BUSINESS_ERROR',
            $e instanceof ModelNotFoundException => 'RESOURCE_NOT_FOUND',
            $e instanceof NotFoundHttpException => 'ENDPOINT_NOT_FOUND',
            $e instanceof MethodNotAllowedHttpException => 'METHOD_NOT_ALLOWED',
            $e instanceof ThrottleRequestsException => 'TOO_MANY_REQUESTS',
            default => 'INTERNAL_ERROR',
        };
    }

    /**
     * Send exception to monitoring service
     */
    private function sendToMonitoringService(Throwable $e): void
    {
        $context = [
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'environment' => app()->environment(),
        ];

        // Check if user is authenticated
        $user = request()->user();
        if ($user !== null) {
            $context['user_id'] = $user->id;
        }

        // Log exception with context
        logger()->error($e->getMessage(), array_merge($context, [
            'exception' => get_class($e),
        ]));
    }

    /**
     * Prepare exception for rendering
     */
    protected function prepareException(Throwable $e)
    {
        // Custom handling for different environments
        if (app()->environment('production')) {
            // Hide sensitive information in production
            if ($e instanceof \PDOException) {
                return new \Exception('Database connection error', 0, $e);
            }
        }

        return parent::prepareException($e);
    }

    /**
     * Render an exception into an HTTP response
     */
    public function render($request, Throwable $e)
    {
        // Handle CSRF token mismatch
        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()
                ->back()
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('error', 'Your session has expired. Please try again.');
        }

        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into an unauthenticated response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'error_code' => 'UNAUTHENTICATED',
            ], 401);
        }

        // Store intended URL for redirect after login
        if (!$request->isMethod('get')) {
            session()->put('url.intended', $request->fullUrl());
        }

        return redirect()->guest(route('login'));
    }

    /**
     * Convert a validation exception into a JSON response
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $exception->errors(),
            'error_code' => 'VALIDATION_ERROR',
        ], $exception->status);
    }
}
