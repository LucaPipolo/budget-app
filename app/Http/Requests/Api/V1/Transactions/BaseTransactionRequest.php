<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Transactions;

use Illuminate\Foundation\Http\FormRequest;

class BaseTransactionRequest extends FormRequest
{
    /**
     * Map input attributes to their corresponding model attributes.
     *
     * This method reads specific keys from the request input and maps them to the
     * model's attribute names, preparing an array for updating the model.
     *
     * @return array An associative array of model attributes to be updated.
     */
    public function mappedAttributes(): array
    {
        $attributeMap = [
            'data.attributes.amount' => 'amount',
            'data.attributes.date' => 'date',
            'data.attributes.notes' => 'notes',
            'data.attributes.accountId' => 'account_id',
            'data.attributes.merchantId' => 'merchant_id',
            'data.attributes.categoryId' => 'category_id',
            'data.attributes.teamId' => 'team_id',
        ];

        $attributesToUpdate = [];

        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }

    /**
     * Prepare the data for validation.
     *
     * This method modifies the input data before validation occurs, specifically
     * ensuring that the date format is correct by appending seconds if necessary.
     *
     * This is necessary because Laravel’s date_format rule cannot natively validate
     * the Y-m-d H:i:s+00 format, because PHP’s DateTime does not recognize a timezone
     * offset with only hours (no colon, no minutes).
     */
    public function prepareForValidation(): void
    {
        if (! $this->has('data.attributes.date') || ! is_string($this->input('data.attributes.date'))) {
            return;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\+\d{2}$/', $this->input('data.attributes.date'))) {
            $this->merge([
                'data.attributes.date' => $this->input('data.attributes.date') . ':00',
            ]);
        }
    }
}
