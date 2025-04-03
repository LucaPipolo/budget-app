<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Accounts;

use App\Enums\AccountTypes;
use App\Rules\LogoPathRule;
use Cknow\Money\Rules\Currency;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

class UpdateAccountRequest extends BaseAccountRequest
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
            'data.attributes.type' => ['sometimes', new Enum(AccountTypes::class)],
            'data.attributes.balance' => 'sometimes|integer|min:0',
            'data.attributes.currency' => ['sometimes', new Currency()],
            'data.attributes.iban' => 'sometimes|string|regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/',
            'data.attributes.swift' => 'sometimes|string|regex:/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/',
            'data.attributes.teamId' => 'sometimes|uuid|exists:teams,id',
            'data.attributes.logoPath' => ['sometimes', 'nullable', 'string', new LogoPathRule('accounts')],
        ];
    }
}
