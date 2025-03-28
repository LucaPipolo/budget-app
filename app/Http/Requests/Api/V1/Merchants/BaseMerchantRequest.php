<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Merchants;

use Illuminate\Foundation\Http\FormRequest;

class BaseMerchantRequest extends FormRequest
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
            'data.attributes.name' => 'name',
            'data.attributes.balance' => 'balance',
            'data.attributes.teamId' => 'team_id',
            'data.attributes.logoPath' => 'logo_path',
        ];

        $attributesToUpdate = [];

        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }
}
