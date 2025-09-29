<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Throwable;

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

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Handle ModelNotFoundException for API routes
        if ($exception instanceof ModelNotFoundException && $request->expectsJson()) {
            $model = class_basename($exception->getModel());

            // Extract ID from the URL for better error messages
            $id = $this->extractIdFromRequest($request);

            return response()->json([
                'message' => "{$model} not found",
                'error' => $id
                    ? "The {$model} with ID {$id} does not exist or has been deleted."
                    : "The requested {$model} could not be found."
            ], 404);
        }

        return parent::render($request, $exception);
    }

    /**
     * Extract ID from request URL for better error messaging
     */
    private function extractIdFromRequest(Request $request): ?string
    {
        $path = $request->path();

        // Extract numeric ID from various API endpoints
        if (preg_match('/research-items\/(\d+)/', $path, $matches)) {
            return $matches[1];
        }

        if (preg_match('/companies\/(\d+)/', $path, $matches)) {
            return $matches[1];
        }

        return null;
    }
}