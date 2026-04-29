<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        return Category::with('children')
            ->whereNull('parent_id')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->fresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
