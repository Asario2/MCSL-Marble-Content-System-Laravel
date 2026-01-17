<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException; // <-- hier
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Route not found.'
                ], 404);
            }

            return redirect()->to('/403'); // oder '/404'
        });

        $this->renderable(function (Throwable $e, $request) {
            if (config('app.debug')) {
                return null;
            }

            return response()->view('errors.generic', [], 500);
        });
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'success' => false,
            'message' => __('validation.failed'),
            'errors' => $exception->errors(),
        ], $exception->status);
    }
}
