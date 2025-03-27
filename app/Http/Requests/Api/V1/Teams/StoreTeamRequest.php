<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Teams;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreTeamRequest extends BaseTeamRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string> Validation rules for the request.
     */
    public function rules(): array
    {
        return [
            'data.attributes.name' => 'required|string|min:3|max:255',
        ];
    }
}
