<?php
namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

trait ValidateRequestException
{
    protected function failedValidation(Validator $validator)
    {
        throw ValidationException::withMessages([
            'error' => [$validator->errors()->first()],
        ]);
    }
}
