<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    protected $table = 'packages';
    protected $fillable = [
        'title',
        'code',
        'price',
        'validity',
        'type_package_id',
        'duration',
        'called',
        'sex',
        'voice_hidden',
        'language',
        'status',
    ];

    public function type_package()
    {
        return $this->belongsTo(TypePackage::class, 'type_package_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_packages', 'package_id', 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
