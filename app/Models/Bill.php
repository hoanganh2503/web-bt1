<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;
    protected $fillable = [
        'address_id',
        'delivery_id',
        'payment_status',
        'total_price',
        'note',
        'status',
        'created_at',
        'updated_at',
    ];

    public function product(){
        return $this->hasMany(ProductBill::class);
    }

    public function getStatus($id = -1){
        if($id == -1)
            return [
                0 => 'Đã đặt hàng',
                1 => 'Đã xác nhận',
                2 => 'Đang giao hàng',
                3 => 'Giao hàng thành công',
                4 => 'Đã hủy'
            ];
        else{
            return $this->getStatus()[$id];
        }
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
