<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Tags;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateTagRequest extends BaseTagRequest
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
            'data.attributes.color' => 'sometimes|hex_color',
            'data.attributes.teamId' => 'sometimes|uuid|exists:teams,id',
        ];
    }
}
