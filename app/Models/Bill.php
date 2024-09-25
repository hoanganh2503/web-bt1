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
        'created_at',
        'updated_at',
    ];

    public function product(){
        return $this->hasMany(ProductBill::class);
    }
}
