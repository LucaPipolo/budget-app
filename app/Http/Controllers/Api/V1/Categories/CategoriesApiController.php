<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Categories;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\Categories\StoreCategoryRequest;
use App\Http\Requests\Api\V1\Categories\UpdateCategoryRequest;
use App\Http\Resources\Api\V1\Categories\CategoryResource;
use App\Models\Category;
use App\Policies\CategoryPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class CategoriesApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = CategoryPolicy::class;

    /**
     * List categories.
     *
     * @return AnonymousResourceCollection The list of categories.
     *
     * @response AnonymousResourceCollection<Illuminate\Pagination\LengthAwarePaginator<CategoryResource>>
     *
     * @throws TokenAbilitiesException
     */
    public function index(): AnonymousResourceCollection
    {
        $this->isAble('viewAny', Category::class, 'read');

        $user = auth()->user();
        $teamIds = $user->allTeams()->pluck('id');

        /** @var Collection<int, Category> $categories */
        $categories = QueryBuilder::for(Category::class)
            ->allowedFilters([
                'name',
                AllowedFilter::exact('teamId', 'team_id')->ignore(null),
                AllowedFilter::exact('type', 'type')->ignore(null),
                AllowedFilter::operator('balance', FilterOperator::DYNAMIC, 'or'),
            ])
            ->allowedSorts([
                'name',
                'balance',
                AllowedSort::field('createdAt', 'created_at'),
                AllowedSort::field('updatedAt', 'updated_at'),
            ])
            ->where(function (Builder $query) use ($teamIds): void {
                $query->whereIn('team_id', $teamIds);
            })
            ->paginate();

        return CategoryResource::collection($categories);
    }

    /**
     * Create category.
     *
     * @param  StoreCategoryRequest  $request  The request containing the category data.
     *
     * @return CategoryResource The created category or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $this->isAble('create', new Category([
            'team_id' => $request->input('data.attributes.teamId'),
        ]), 'create');

        $category = Category::create($request->mappedAttributes());

        return new CategoryResource($category);
    }

    /**
     * Show category.
     *
     * @param  string  $category_id  The ID of the category to display.
     *
     * @throws TokenAbilitiesException
     */
    public function show(string $category_id): CategoryResource
    {
        /** @var Category $category */
        $category = QueryBuilder::for(Category::class)
            ->findOrFail($category_id);

        $this->isAble('view', $category, 'read');

        return new CategoryResource($category);
    }

    /**
     * Replace category.
     *
     * @param  StoreCategoryRequest  $request  The request containing the updated category data.
     * @param  string  $category_id  The ID of the category to update.
     *
     * @return CategoryResource The updated category or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function replace(StoreCategoryRequest $request, string $category_id): CategoryResource
    {
        $category = Category::findOrFail($category_id);
        $category->team_id = $request->input('data.attributes.teamId');
        $this->isAble('update', $category, 'update');

        $attributes = array_merge(
            ['balance' => '0'],
            $request->mappedAttributes()
        );
        $category->update($attributes);

        return new CategoryResource($category);
    }

    /**
     * Update category.
     *
     * @param  UpdateCategoryRequest  $request  The request containing the updated category data.
     * @param  string  $category_id  The ID of the category to update.
     *
     * @return CategoryResource|JsonResponse The updated category or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function update(UpdateCategoryRequest $request, string $category_id): CategoryResource|JsonResponse
    {
        $category = Category::findOrFail($category_id);
        $request->has('data.attributes.teamId') && $category->team_id = $request->input('data.attributes.teamId');
        $this->isAble('update', $category, 'update');

        $category->update($request->mappedAttributes());

        return new CategoryResource($category);
    }

    /**
     * Delete category.
     *
     * @param  string  $category_id  The ID of the category to delete.
     *
     * @return JsonResponse The response indicating the result of the operation.
     *
     * @throws TokenAbilitiesException
     */
    public function destroy(string $category_id): JsonResponse
    {
        $category = Category::findOrFail($category_id);
        $this->isAble('delete', $category, 'delete');

        $category->delete();

        return response()->json([], 204);
    }
}
