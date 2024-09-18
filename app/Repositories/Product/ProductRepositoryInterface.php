<?php

namespace App\Repositories\Product;

use App\Http\Requests\ProductRequest;
use App\Repositories\BaseRepositoryInterface;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getListProducts(ProductRequest $request, $perpage = 10);
    public function getDetailProduct($id);
    public function addProduct(ProductRequest $request);
    public function updateProduct(ProductRequest $request);
    public function deleteProduct($id);
    public function getDetailChild($id);
    public function addFeatureProduct(ProductRequest $request);
    public function editFeatureProduct(ProductRequest $request);
    public function deleteFeatureProduct($id);

}
