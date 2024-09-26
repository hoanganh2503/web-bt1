<?php

namespace App\Repositories\Bill;

use App\Http\Requests\BillRequest;
use App\Repositories\BaseRepositoryInterface;

interface BillRepositoryInterface extends BaseRepositoryInterface
{
    public function getListBills(BillRequest $request, $perpage = 10);
    public function getDetailBill($id);
    public function changeStatus(BillRequest $request);

}
