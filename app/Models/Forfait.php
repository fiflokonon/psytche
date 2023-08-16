<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forfait extends Model
{
    use HasFactory;
    protected $table = "forfaits";
    protected $fillables = ["type_forfait_id", "nbrminutes", "validity", "price",
                            "voice_hidden", "libelle", "code"];
}
