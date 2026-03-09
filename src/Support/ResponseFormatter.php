<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Support;

use Illuminate\Http\JsonResponse;

final class ResponseFormatter
{
    /**
     * Success response: 200 or custom status code.
     */
    public static function success(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Created response: 201.
     */
    public static function created(mixed $data = null, string $message = 'Created successfully.'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * Error response.
     */
    public static function error(string $message = 'An error occurred.', int $status = 400, mixed $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    /**
     * Unauthorized response: 401.
     */
    public static function unauthorized(string $message = 'Unauthorized.'): JsonResponse
    {
        return self::error($message, 401);
    }

    /**
     * Forbidden response: 403.
     */
    public static function forbidden(string $message = 'Forbidden.'): JsonResponse
    {
        return self::error($message, 403);
    }

    /**
     * Too Many Requests response: 429.
     */
    public static function tooManyRequests(string $message = 'Too many requests.'): JsonResponse
    {
        return self::error($message, 429);
    }
}
