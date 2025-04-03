<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Merchants;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Storage;

class UpdateMerchantRequest extends BaseMerchantRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string> Validation rules for the request.
     */
    public function rules(): array
    {
        return [
            'data.attributes.name' => 'sometimes|string|min:3|max:255',
            'data.attributes.balance' => 'sometimes|integer|min:0',
            'data.attributes.teamId' => 'sometimes|uuid|exists:teams,id',
            'data.attributes.logoPath' => [
                'sometimes',
                'nullable',
                'string',
                function (string $attribute, string $value, \Closure $fail): void {
                    if ($value !== '' && ! Storage::disk('public')->exists($value)) {
                        $fail("Field {$attribute} refers to a non existing file.");
                    }

                    if ($value !== '' && ! str_starts_with($value, 'merchants/')) {
                        $fail("The {$attribute} should start with 'merchants/'.");
                    }
                },
            ],
        ];
    }
}
