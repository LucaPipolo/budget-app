<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;

trait HasIncludedResources
{
    /**
     * Prepares included resources for JSON API responses.
     *
     * @param  Model|Collection  $models  The models/collection to process.
     * @param  array  $includeMappings  Map of include parameters to relationship accessors and resource classes.
     * @param  string|null  $includeParams  Comma-separated include parameters.
     *
     * @return SupportCollection The prepared included resources.
     */
    protected function prepareIncludedResources(
        Model|Collection $models,
        array $includeMappings,
        ?string $includeParams = null
    ): SupportCollection {
        $included = collect();
        $includeParams = explode(',', $includeParams ?? request()->include ?? '');
        $modelsCollection = $models instanceof Model ? collect([$models]) : $models;

        foreach ($includeMappings as $param => $config) {
            if (in_array($param, $includeParams)) {
                [$accessor, $resourceClass] = $config;

                $relatedModels = $modelsCollection->flatMap(
                    fn (Model $model) => is_string($accessor) ? $model->{$accessor} : $accessor($model)
                );

                $included = $included->concat($resourceClass::collection($relatedModels));
            }
        }

        return $included;
    }
}
