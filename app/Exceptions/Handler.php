<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            return $this->renderJsonError($request, $e);
        }

        return parent::render($request, $e);
    }

    protected function renderJsonError(Request $request, Throwable $e): \Illuminate\Http\JsonResponse
    {
        $status  = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        $message = match ($status) {
            401 => 'Unauthenticated.',
            403 => 'Forbidden.',
            404 => 'Not found.',
            422 => $e->getMessage(),
            429 => 'Too many requests.',
            default => app()->isProduction() ? 'Server error.' : $e->getMessage(),
        };

        return response()->json(['message' => $message], $status);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}