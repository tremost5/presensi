<?php

namespace App\Compat\CodeIgniter\HTTP;

use CodeIgniter\HTTP\ResponseInterface;
use Illuminate\Http\JsonResponse;

class CiResponse implements ResponseInterface
{
    private int $statusCode = 200;

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function setJSON(array $payload): JsonResponse
    {
        return response()->json($payload, $this->statusCode);
    }
}
