<?php
namespace App\Traits;

use Illuminate\Validation\ValidationException;

trait ThrowsValidationException
{
    /**
     * Throw validation exception with custom messages.
     *
     * @param  array|string  $messages
     * @param  string|null   $key
     * @throws ValidationException
     */
    public function throwValidation($messages, ?string $key = null)
    {
        if (is_string($messages)) {
            $messages = [$key ?? 'error' => $messages];
        }

        throw ValidationException::withMessages($messages);
    }
}
