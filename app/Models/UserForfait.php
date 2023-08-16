<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserForfait extends Model
{
    use HasFactory;
    protected $table = "user_forfaits";
    protected $fillables = [ "user_id", "users", "forfait_id", "date_by",
                            "nbr_minutes_rest", "statut", "payement_term",
                            "payement_statut" ];
}
