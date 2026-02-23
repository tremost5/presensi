<?php

namespace App\Compat\Validation;

use Illuminate\Validation\Validator;

class CiValidator
{
    public function __construct(private readonly Validator $validator)
    {
    }

    public function getErrors(): array
    {
        return $this->validator->errors()->toArray();
    }
}
