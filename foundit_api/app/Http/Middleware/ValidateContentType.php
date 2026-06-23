<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateContentType
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $method = $request->method();
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $contentType = $request->header('Content-Type');

            if (!$contentType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Content-Type header is missing',
                ], 415);
            }

            $allowedTypes = [
                'application/json',
                'multipart/form-data',
                'application/x-www-form-urlencoded',
            ];

            $isValid = false;
            foreach ($allowedTypes as $type) {
                if (str_starts_with(strtolower($contentType), $type)) {
                    $isValid = true;
                    break;
                }
            }

            if (!$isValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unsupported Media Type',
                ], 415);
            }
        }

        return $next($request);
    }
}
