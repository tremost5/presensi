<?php

namespace CodeIgniter;

use App\Compat\CodeIgniter\HTTP\CiRequest;
use App\Compat\CodeIgniter\HTTP\CiResponse;
use App\Compat\Validation\CiValidator;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Illuminate\Support\Facades\Validator;
use Psr\Log\LoggerInterface;

class Controller
{
    protected CiRequest $request;
    protected CiResponse $response;
    protected array $helpers = [];
    protected ?CiValidator $validator = null;

    public function __construct()
    {
        $this->request = new CiRequest(request());
        $this->response = new CiResponse();

        if (!empty($this->helpers)) {
            helper($this->helpers);
        }
    }

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ): void {
        // Laravel resolves request/response through helpers.
    }

    protected function validate(array $rules, array $messages = []): bool
    {
        $data = array_merge(request()->query(), request()->post());
        $converted = [];

        foreach ($rules as $field => $rule) {
            $converted[$field] = $this->mapRule((string) $rule);
        }

        $validator = Validator::make($data, $converted, $messages);

        if ($validator->fails()) {
            $this->validator = new CiValidator($validator);
            return false;
        }

        $this->validator = new CiValidator($validator);
        return true;
    }

    private function mapRule(string $rule): array
    {
        $out = [];
        $parts = array_filter(explode('|', $rule));

        foreach ($parts as $part) {
            if ($part === 'required') {
                $out[] = 'required';
                continue;
            }

            if ($part === 'permit_empty') {
                $out[] = 'nullable';
                continue;
            }

            if ($part === 'valid_email') {
                $out[] = 'email';
                continue;
            }

            if (preg_match('/^min_length\[(\d+)\]$/', $part, $m)) {
                $out[] = 'min:' . $m[1];
                continue;
            }

            if (preg_match('/^max_length\[(\d+)\]$/', $part, $m)) {
                $out[] = 'max:' . $m[1];
                continue;
            }

            if (preg_match('/^is_unique\[([^\.]+)\.([^\]]+)\]$/', $part, $m)) {
                $out[] = 'unique:' . $m[1] . ',' . $m[2];
                continue;
            }

            if (preg_match('/^matches\[([^\]]+)\]$/', $part, $m)) {
                $out[] = 'same:' . $m[1];
                continue;
            }

            if (preg_match('/^valid_date\[[^\]]+\]$/', $part)) {
                $out[] = 'date';
                continue;
            }

            if (preg_match('/^uploaded\[[^\]]+\]$/', $part)) {
                $out[] = 'required';
                $out[] = 'file';
                continue;
            }

            if (preg_match('/^is_image\[[^\]]+\]$/', $part)) {
                $out[] = 'image';
                continue;
            }

            if (preg_match('/^mime_in\[[^,]+,(.+)\]$/', $part, $m)) {
                $mimes = collect(explode(',', $m[1]))
                    ->map(fn (string $mime) => trim(str_replace('image/', '', $mime)))
                    ->implode(',');
                $out[] = 'mimes:' . $mimes;
                continue;
            }

            if (preg_match('/^max_size\[[^,]+,(\d+)\]$/', $part, $m)) {
                $out[] = 'max:' . $m[1];
            }
        }

        return $out;
    }
}
