<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Categories;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Categories\CategoryTransactionsResource;
use App\Http\Resources\Api\V1\Transactions\TransactionResource;
use App\Models\Category;
use App\Policies\CategoryPolicy;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryTransactionsApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = CategoryPolicy::class;

    /**
     * List categories' transaction relationships.
     *
     * @return CategoryTransactionsResource The list of categories' transaction relationships.
     *
     * @throws TokenAbilitiesException
     */
    public function transactionsRelationships(Category $category): CategoryTransactionsResource
    {
        $this->isAble('view', $category, 'read');

        return new CategoryTransactionsResource($category);
    }

    /**
     * List categories' transactions.
     *
     * @return AnonymousResourceCollection The list of categories' transactions.
     *
     * @throws TokenAbilitiesException
     */
    public function transactions(Category $category): AnonymousResourceCollection
    {
        $this->isAble('view', $category, 'read');

        return TransactionResource::collection($category->transactions);
    }
}
