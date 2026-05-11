<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\CacheService;

class CategoryObserver
{
    public function __construct(private CacheService $cacheService) {}

    public function saved(Category $category): void  { $this->cacheService->forgetCategories(); }
    public function deleted(Category $category): void { $this->cacheService->forgetCategories(); }
}