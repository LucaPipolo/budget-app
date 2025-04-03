<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Storage;

class LogoPathRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== '' && ! Storage::disk('public')->exists($value)) {
            $fail("The {$attribute} field refers to a non existing file.");
        }

        if ($value !== '' && ! str_starts_with($value, 'merchants/')) {
            $fail("The {$attribute} field should start with 'merchants/'.");
        }
    }
}
