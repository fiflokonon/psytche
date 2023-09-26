<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'ASC')->get();
        return view('dashboard.userlist', [
            'users' => $users
        ]);
    }

    public function connectedUsers()
    {
        $fiveMinutesAgo = Carbon::now()->subMinutes(5);
        $users = User::where('last_connexion', '>', $fiveMinutesAgo)->get();
        return view('dashboard.connected_userlist', [
            'users' => $users
        ]);
    }
}
