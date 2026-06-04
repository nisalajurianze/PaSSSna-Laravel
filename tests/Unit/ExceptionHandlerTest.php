<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Exceptions\BusinessException;
use App\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class ExceptionHandlerTest extends TestCase
{
    protected Handler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = app(Handler::class);
    }

    /**
     * Test BusinessException status code handling
     */
    public function test_business_exception_returns_422_status_code(): void
    {
        $exception = new BusinessException('Business logic error occurred');

        $statusCode = $this->invokePrivateMethod($this->handler, 'getStatusCode', [$exception]);

        $this->assertEquals(422, $statusCode);
    }

    /**
     * Test BusinessException error code handling
     */
    public function test_business_exception_returns_business_error_code(): void
    {
        $exception = new BusinessException('Business logic error occurred');

        $errorCode = $this->invokePrivateMethod($this->handler, 'getErrorCode', [$exception]);

        $this->assertEquals('BUSINESS_ERROR', $errorCode);
    }

    /**
     * Test BusinessException error message handling
     */
    public function test_business_exception_returns_proper_error_message(): void
    {
        $exception = new BusinessException('Business logic error occurred');

        $errorMessage = $this->invokePrivateMethod($this->handler, 'getErrorMessage', [$exception, 422]);

        $this->assertEquals('Business logic error.', $errorMessage);
    }

    /**
     * Test BusinessException API response structure
     */
    public function test_business_exception_api_response_structure(): void
    {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Accept', 'application/json');

        $exception = new BusinessException('Business logic error occurred');

        $response = $this->handler->render($request, $exception);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Business logic error.', $responseData['message']);
        $this->assertEquals('BUSINESS_ERROR', $responseData['error_code']);
        $this->assertArrayHasKey('timestamp', $responseData);
    }

    /**
     * Test various exception status codes
     */
    public function test_various_exceptions_return_correct_status_codes(): void
    {
        $testCases = [
            [new AuthenticationException('Unauthenticated'), 401],
            [ValidationException::withMessages(['field' => ['The field is required.']]), 422],
            [new BusinessException('Business error'), 422],
            [new ModelNotFoundException(), 404],
            [new NotFoundHttpException(), 404],
            [new MethodNotAllowedHttpException([]), 405],
            [new ThrottleRequestsException('Too many requests'), 429],
            [new ServiceUnavailableHttpException(null, 'Maintenance'), 503],
            [new \Exception('Generic error'), 500],
        ];

        foreach ($testCases as [$exception, $expectedStatus]) {
            $statusCode = $this->invokePrivateMethod($this->handler, 'getStatusCode', [$exception]);
            $this->assertEquals($expectedStatus, $statusCode, "Failed for " . get_class($exception));
        }
    }

    /**
     * Test various exception error codes
     */
    public function test_various_exceptions_return_correct_error_codes(): void
    {
        $testCases = [
            [new AuthenticationException('Unauthenticated'), 'UNAUTHENTICATED'],
            [ValidationException::withMessages(['field' => ['The field is required.']]), 'VALIDATION_ERROR'],
            [new BusinessException('Business error'), 'BUSINESS_ERROR'],
            [new ModelNotFoundException(), 'RESOURCE_NOT_FOUND'],
            [new NotFoundHttpException(), 'ENDPOINT_NOT_FOUND'],
            [new MethodNotAllowedHttpException([]), 'METHOD_NOT_ALLOWED'],
            [new ThrottleRequestsException('Too many requests'), 'TOO_MANY_REQUESTS'],
            [new \Exception('Generic error'), 'INTERNAL_ERROR'],
        ];

        foreach ($testCases as [$exception, $expectedCode]) {
            $errorCode = $this->invokePrivateMethod($this->handler, 'getErrorCode', [$exception]);
            $this->assertEquals($expectedCode, $errorCode, "Failed for " . get_class($exception));
        }
    }

    /**
     * Test debug information is included in development
     */
    public function test_debug_information_included_in_development(): void
    {
        config(['app.debug' => true]);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Accept', 'application/json');

        $exception = new BusinessException('Business logic error occurred');

        $response = $this->handler->render($request, $exception);
        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('debug', $responseData);
        $this->assertEquals(BusinessException::class, $responseData['debug']['exception']);
    }

    /**
     * Test debug information is excluded in production
     */
    public function test_debug_information_excluded_in_production(): void
    {
        config(['app.debug' => false]);

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Accept', 'application/json');

        $exception = new BusinessException('Business logic error occurred');

        $response = $this->handler->render($request, $exception);
        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayNotHasKey('debug', $responseData);
    }

    /**
     * Helper method to invoke private methods
     */
    private function invokePrivateMethod($object, $method, $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
