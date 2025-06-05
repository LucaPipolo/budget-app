<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Transactions;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateTransactionRequest extends BaseTransactionRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string> Validation rules for the request.
     */
    public function rules(): array
    {
        return [
            'data.attributes.amount' => 'sometimes|integer',
            'data.attributes.date' => 'sometimes|date_format:Y-m-d H:i:sP',
            'data.attributes.notes' => 'sometimes|string|max:255',
            'data.attributes.accountId' => 'sometimes|uuid|exists:accounts,id',
            'data.attributes.merchantId' => 'sometimes|uuid|exists:merchants,id',
            'data.attributes.categoryId' => 'sometimes|uuid|exists:categories,id',
            'data.attributes.teamId' => 'sometimes|uuid|exists:teams,id',
        ];
    }
}
