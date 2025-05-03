<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AppException extends Exception
{
    protected $userMessage;
    protected $httpCode;
    protected $retryable;

    public function __construct(
        string $message = 'An error occurred',
        string $userMessage = 'Something went wrong',
        int $httpCode = 500,
        bool $retryable = true
    ) {
        parent::__construct($message);
        $this->userMessage = $userMessage;
        $this->httpCode = $httpCode;
        $this->retryable = $retryable;
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function isRetryable(): bool
    {
        return $this->retryable;
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $this->getUserMessage(),
                'retryable' => $this->isRetryable(),
                'retry_url' => url()->current()
            ], $this->getHttpCode());
        }

        return response()->view('errors.generalError', [
            'exception' => $this,
            'retryable' => $this->isRetryable()
        ], $this->getHttpCode());
    }
}
