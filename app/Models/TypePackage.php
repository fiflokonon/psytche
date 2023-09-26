<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypePackage extends Model
{
    use HasFactory;
    protected $table = 'type_packages';
    protected $fillable = [
        'title',
        'status'
    ];

    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}
