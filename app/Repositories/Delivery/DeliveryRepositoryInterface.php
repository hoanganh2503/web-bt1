<?php

namespace App\Repositories\Delivery;

use App\Http\Requests\DeliveryRequest;
use App\Repositories\BaseRepositoryInterface;

interface DeliveryRepositoryInterface extends BaseRepositoryInterface
{
    public function getListDeliveries(DeliveryRequest $request, $perpage = 10);
    public function getDetailDelivery($id);
    public function addDelivery(DeliveryRequest $request);
    public function updateDelivery(DeliveryRequest $request);
    public function deleteDelivery($id);
}
