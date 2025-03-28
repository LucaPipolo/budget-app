<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Uploads;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreUploadRequest extends BaseUploadRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string> Validation rules for the request.
     */
    public function rules(): array
    {
        return [
            'entity' => 'required|string|in:merchants',
            'file' => 'required|file|max:5120|mimetypes:image/png,image/jpeg,image/jpg,image/svg',
        ];
    }
}
