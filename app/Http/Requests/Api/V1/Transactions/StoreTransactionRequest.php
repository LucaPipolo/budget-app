<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Transactions;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreTransactionRequest extends BaseTransactionRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string> Validation rules for the request.
     */
    public function rules(): array
    {
        return [
            'data.attributes.amount' => 'required|integer',
            'data.attributes.date' => 'required|date_format:Y-m-d H:i:sP',
            'data.attributes.notes' => 'sometimes|string|max:255',
            'data.attributes.accountId' => 'required|uuid|exists:accounts,id',
            'data.attributes.merchantId' => 'required|uuid|exists:merchants,id',
            'data.attributes.categoryId' => 'required|uuid|exists:categories,id',
            'data.attributes.teamId' => 'required|uuid|exists:teams,id',
        ];
    }
}
