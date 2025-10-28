<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            // Für API-Anfragen JSON zurückgeben
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Route not found.'
                ], 403);
            }

            // Für Web-Anfragen zur 404-Seite weiterleiten
            return redirect('/403');
        });

        // Allgemeiner Fallback für alle anderen Exceptions
        $this->renderable(function (Throwable $e, $request) {
            if (config('app.debug')) {
                return null; // Debug-Modus: Zeige normale Fehlerseite
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
