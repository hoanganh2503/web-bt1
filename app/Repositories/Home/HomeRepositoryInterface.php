<?php

namespace App\Repositories\Home;

use App\Http\Requests\HomeRequest;
use App\Repositories\BaseRepositoryInterface;

interface HomeRepositoryInterface extends BaseRepositoryInterface
{
    public function home(HomeRequest $request);
    public function product($id);
    public function addToCart(HomeRequest $request);
    public function cart(HomeRequest $request);

}
