<?php

namespace App\Repositories\Category;

use App\Http\Requests\CategoryRequest;
use App\Repositories\BaseRepositoryInterface;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getListCategories(CategoryRequest $request, $perpage = 10);
    public function getDetailCategory($id);
    public function addCategory(CategoryRequest $request);
    public function updateCategory(CategoryRequest $request);
    public function deleteCategory($id);
}
