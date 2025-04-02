<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Uploads;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\Uploads\StoreUploadRequest;
use App\Http\Resources\Api\V1\Uploads\UploadResource;
use Illuminate\Support\Facades\Storage;

class UploadApiController extends ApiController
{
    /**
     * Create upload.
     *
     * @param  StoreUploadRequest  $request  The request containing the upload file data.
     *
     * @return UploadResource The created upload file or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function store(StoreUploadRequest $request): UploadResource
    {
        $user = auth()->user();

        if (! $user || ! $user->tokenCan('create')) {
            throw new TokenAbilitiesException();
        }

        $entity = $request->input('entity');
        $file = $request->file('file');

        Storage::makeDirectory($entity);

        $path = $file->store($entity, 'public');

        return new UploadResource($path);
    }
}
