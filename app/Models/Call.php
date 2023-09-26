<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;
    protected $table = "calls";
    protected $fillable = [
        'caller_id',
        'called_id',
        'sex',
        'languages',
        'country',
        'voice_hidden',
        'duration',
        'status'
    ];

    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function called()
    {
        return $this->belongsTo(User::class, 'called_id');
    }
}
