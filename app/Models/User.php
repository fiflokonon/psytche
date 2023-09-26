<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $fillable = [
        'last_name',
        'first_name',
        'email',
        'phone',
        'call_id',
        'role',
        'sex',
        'country',
        'voice_hidden',
        'balance',
        'status',
        'last_connexion'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'transactions', 'user_id', 'package_id')
            ->withPivot([
                'bought_at',
                'remaining_time',
                'package_status'
                ]);
    }

    public function active_package()
    {
        return $this->belongsToMany(Package::class, 'transactions', 'user_id', 'package_id')
            ->wherePivot('package_status', true)->limit(1)
            ->orderByDesc('transactions.id')
            ->withPivot([
                'bought_at',
                'remaining_time',
                'package_status'
            ]);
    }

    public function deactivateAllPackages()
    {
        $this->packages()->each(function ($package) {
            $this->packages()->updateExistingPivot($package->id, ['package_status' => false]);
        });
    }

    public function callHistory()
    {
        return $this->hasMany(Call::class, 'caller_id', 'id')
            ->orWhere('called_id', $this->id)
            ->orderBy('created_at', 'desc');
    }

    public function getRandomCallId()
    {
        $fiveMinutesAgo = Carbon::now()->subMinutes(5);

        $randomUserId = DB::table('users')
            ->where('id', '<>', $this->id)
            ->where('last_connexion', '>', $fiveMinutesAgo)
            ->inRandomOrder()
            ->value('call_id');
        if ($randomUserId) {
            return $randomUserId;
        }

        return null;
    }

    public function gains()
    {
        return $this->hasMany(Call::class, 'called_id', 'id')->sum('benefit');
    }

    public function totalWithdraw()
    {
        $type = TypeTransaction::where('code', 'withdraw')->first();
        return $this->hasMany(Transaction::class)->where('type_transaction_id', $type->id)->sum('amount');
    }

    public function newBalance()
    {
        return $this->gains() - $this->totalWithdraw();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function updateLastConnexion()
    {
        $this->last_connexion = Carbon::now();
        $this->save();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id');
    }
}
