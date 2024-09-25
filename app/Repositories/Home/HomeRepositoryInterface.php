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
    public function profile(HomeRequest $request);
    public function changeProfile(HomeRequest $request);

    public function getListAddresses(HomeRequest $request);
    public function getDetailAddress($id);
    public function addAddress(HomeRequest $request);
    public function updateAddress(HomeRequest $request);
    public function deleteAddress($id);

    public function checkout(HomeRequest $request);
    public function order(HomeRequest $request);
    public function orderHistory(HomeRequest $request);
    public function orderDetail(HomeRequest $request);

}
