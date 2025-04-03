<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Uploads;

use App\Enums\UploadEntities;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

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
            'entity' => ['required', new Enum(UploadEntities::class)],
            'file' => 'required|file|max:5120|mimetypes:image/png,image/jpeg,image/jpg,image/svg',
        ];
    }
}
