<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $fillable = [
    'type_transaction_id',
    'amount',
    'token',
    'phone',
    'payment_status',
    'transaction_status',
    'payment_details',
    'custom_data',
    'user_id',
    'package_id',
    'bought_at',
    'remaining_time',
    'package_status',
    'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type_transaction()
    {
        return $this->belongsTo(TypeTransaction::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
