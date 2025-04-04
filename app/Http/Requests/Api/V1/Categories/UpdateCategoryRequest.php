<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Categories;

use App\Enums\CategoryTypes;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

class UpdateCategoryRequest extends BaseCategoryRequest
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
            'data.attributes.type' => ['sometimes', new Enum(CategoryTypes::class)],
            'data.attributes.balance' => 'sometimes|integer|min:0',
            'data.attributes.teamId' => 'sometimes|uuid|exists:teams,id',
        ];
    }
}
