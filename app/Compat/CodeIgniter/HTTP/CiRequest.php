<?php

namespace App\Compat\CodeIgniter\HTTP;

use CodeIgniter\HTTP\RequestInterface;
use Illuminate\Http\Request;

class CiRequest implements RequestInterface
{
    private array $postOverrides = [];

    public function __construct(private readonly Request $request)
    {
    }

    public function getPost(?string $key = null, mixed $default = null): mixed
    {
        $all = array_merge($this->request->post(), $this->postOverrides);
        return $key === null ? $all : ($all[$key] ?? $default);
    }

    public function getGet(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? $this->request->query() : $this->request->query($key, $default);
    }

    public function setGlobal(string $method, array $values): void
    {
        if (strtolower($method) === 'post') {
            $this->postOverrides = $values;
        }
    }

    public function getFile(string $key): ?CiUploadedFile
    {
        $file = $this->request->file($key);
        if (!$file) {
            return null;
        }

        return new CiUploadedFile($file);
    }

    public function getJSON(bool $assoc = true): mixed
    {
        return json_decode((string) $this->request->getContent(), $assoc);
    }

    public function getIPAddress(): ?string
    {
        return $this->request->ip();
    }

    public function getUserAgent(): CiUserAgent
    {
        return new CiUserAgent((string) $this->request->userAgent());
    }

    public function isAJAX(): bool
    {
        return $this->request->ajax();
    }

    public function getUri(): string
    {
        return $this->request->fullUrl();
    }

    public function getMethod(): string
    {
        return $this->request->method();
    }
}
