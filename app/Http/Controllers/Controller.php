<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

abstract class Controller
{
    protected function sendResponse(mixed $data, string $message = 'Success', int $code = 200, mixed $errors = null): JsonResponse
    {
        $statusText = ($code >= 200 && $code < 300) ? 'success' : 'error';

        $response = [
            'status' => $statusText,
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => $errors,
        ];

        if ($data instanceof AnonymousResourceCollection && $data->resource instanceof AbstractPaginator) {
            $paginated = $data->resource->toArray();
            $response['data'] = $data;
            $response['meta'] = [
                'current_page' => $paginated['current_page'] ?? null,
                'last_page' => $paginated['last_page'] ?? null,
                'per_page' => $paginated['per_page'] ?? null,
                'total' => $paginated['total'] ?? null,
            ];
        } elseif ($data instanceof AbstractPaginator) {
            $paginated = $data->toArray();
            $response['data'] = $paginated['data'];
            $response['meta'] = [
                'current_page' => $paginated['current_page'] ?? null,
                'last_page' => $paginated['last_page'] ?? null,
                'per_page' => $paginated['per_page'] ?? null,
                'total' => $paginated['total'] ?? null,
            ];
        } else {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}