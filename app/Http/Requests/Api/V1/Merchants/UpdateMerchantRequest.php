<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Merchants;

use Illuminate\Contracts\Validation\ValidationRule;

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
        ];
    }
}
