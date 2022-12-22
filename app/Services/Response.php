<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class Response
{
    protected $data;

    protected $errors;

    protected $message;

    protected $statusCode;

    public function empty(): JsonResponse
    {
        return response()->json('', 204);
    }

    public function fail(): JsonResponse
    {
        $data = [
            'message' => $this->message,
            'errors' => $this->errors,
        ];

        return response()->json($data, $this->statusCode ?? 400);
    }

    public function success(): JsonResponse
    {
        $data = [
            'data' => $this->data,
        ];

        return response()->json($data, $this->statusCode ?? 200);
    }

    public function message($message = ''): static
    {
        $this->message = $message;

        return $this;
    }

    public function content($data = []): static
    {
        $this->data = $data;

        return $this;
    }

    public function status($code): static
    {
        $this->statusCode = $code;

        return $this;
    }

    public function errors($errors)
    {
        $this->errors = $errors;

        return $this;
    }
}
