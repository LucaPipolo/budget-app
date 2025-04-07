<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Tags;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\Tags\StoreTagRequest;
use App\Http\Requests\Api\V1\Tags\UpdateTagRequest;
use App\Http\Resources\Api\V1\Tags\TagResource;
use App\Models\Tag;
use App\Policies\TagPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class TagsApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = TagPolicy::class;

    /**
     * List tags.
     *
     * @return AnonymousResourceCollection The list of tags.
     *
     * @response AnonymousResourceCollection<Illuminate\Pagination\LengthAwarePaginator<TagResource>>
     *
     * @throws TokenAbilitiesException
     */
    public function index(): AnonymousResourceCollection
    {
        $this->isAble('viewAny', Tag::class, 'read');

        $user = auth()->user();
        $teamIds = $user->allTeams()->pluck('id');

        /** @var Collection<int, Tag> $tags */
        $tags = QueryBuilder::for(Tag::class)
            ->allowedFilters([
                'name',
                AllowedFilter::exact('teamId', 'team_id')->ignore(null),
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

        return TagResource::collection($tags);
    }

    /**
     * Create tag.
     *
     * @param  StoreTagRequest  $request  The request containing the tag data.
     *
     * @return TagResource The created tag or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function store(StoreTagRequest $request): TagResource
    {
        $this->isAble('create', new Tag([
            'team_id' => $request->input('data.attributes.teamId'),
        ]), 'create');

        $tag = Tag::create($request->mappedAttributes());

        return new TagResource($tag);
    }

    /**
     * Show tag.
     *
     * @param  string  $tag_id  The ID of the tag to display.
     *
     * @throws TokenAbilitiesException
     */
    public function show(string $tag_id): TagResource
    {
        /** @var Tag $tag */
        $tag = QueryBuilder::for(Tag::class)
            ->findOrFail($tag_id);

        $this->isAble('view', $tag, 'read');

        return new TagResource($tag);
    }

    /**
     * Replace tag.
     *
     * @param  StoreTagRequest  $request  The request containing the updated tag data.
     * @param  string  $tag_id  The ID of the tag to update.
     *
     * @return TagResource The updated tag or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function replace(StoreTagRequest $request, string $tag_id): TagResource
    {
        $tag = Tag::findOrFail($tag_id);
        $tag->team_id = $request->input('data.attributes.teamId');
        $this->isAble('update', $tag, 'update');

        $attributes = array_merge(
            ['balance' => '0'],
            $request->mappedAttributes()
        );
        $tag->update($attributes);

        return new TagResource($tag);
    }

    /**
     * Update tag.
     *
     * @param  UpdateTagRequest  $request  The request containing the updated tag data.
     * @param  string  $tag_id  The ID of the tag to update.
     *
     * @return TagResource|JsonResponse The updated tag or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function update(UpdateTagRequest $request, string $tag_id): TagResource|JsonResponse
    {
        $tag = Tag::findOrFail($tag_id);
        $request->has('data.attributes.teamId') && $tag->team_id = $request->input('data.attributes.teamId');
        $this->isAble('update', $tag, 'update');

        $tag->update($request->mappedAttributes());

        return new TagResource($tag);
    }

    /**
     * Delete tag.
     *
     * @param  string  $tag_id  The ID of the tag to delete.
     *
     * @return JsonResponse The response indicating the result of the operation.
     *
     * @throws TokenAbilitiesException
     */
    public function destroy(string $tag_id): JsonResponse
    {
        $tag = Tag::findOrFail($tag_id);
        $this->isAble('delete', $tag, 'delete');

        $tag->delete();

        return response()->json([], 204);
    }
}
