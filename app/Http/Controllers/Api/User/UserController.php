<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function Laravel\Prompts\note;

class UserController extends Controller
{
    public function initUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'call_id' => [
                'required', 'string', 'max:255',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('call_id', $request->call_id)->where('id', '<>', $request->id);
                }),
            ],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorMessage = $errors->first();
            return response()->json(['success' => false, 'message' => $errorMessage], 400);
        }

        try {
            $user = User::create([
                'call_id' => $request->call_id,
                'last_connexion' => now(),
                'status' => true
            ]);
            $package = Package::where('title', 'Welcome Pack')->first();
            $user->packages()->attach($package,
                [
                    'bought_at' => now(),
                    'remaining_time' => '00:10:00',
                    'package_status' => true
                ]);
            $user = User::with('active_package')->where('call_id', $request->call_id)->first();
            return response()->json(['success' => true, 'response' => $user]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 400);
        }
    }

    public function infos(string $call_id)
    {
        $user = User::where('call_id', $call_id)->first();
        if (!$user)
        {
            return response()->json(['success' => false, 'message' => 'Utilisateur indisponible'], 404);
        }
        else{
            $user->active_package = $user->active_package->first()[0];
            $user->balance = $user->newBalance();
            $user->callHistory = $user->callHistory()->get();
            return response()->json(['success' => true, 'response' => $user]);
        }
    }

    public function active_package(string $call_id)
    {
        $user = User::where('call_id', $call_id)->first();
        if (!$user)
        {
            return response()->json(['success' => false, 'message' => 'Utilisateur indisponible'], 404);
        }
        else{
            if ($user->active_package->isNotEmpty())
                return response()->json(['success' => true, 'response' => $user->active_package->first()]);
            else
                return response()->json(['success' => false, 'message' => 'Aucun forfait actif']);
        }
    }

    public function calls(string $call_id)
    {
        $user = User::where('call_id', $call_id)->first();
        if (!$user)
        {
            return response()->json(['success' => false, 'message' => 'Utilisateur indisponible'], 404);
        }
        else{
            if ($user->callHistory->isNotEmpty())
                return response()->json(['success' => true, 'response' => $user->callHistory]);
            else
                return response()->json(['success' => false, 'message' => 'Any call'], 404);
        }
    }

    public function userPackages(string $call_id)
    {
        $user = User::where('call_id', $call_id)->first();
        if (!$user)
        {
            return response()->json(['success' => false, 'message' => 'Utilisateur indisponible'], 404);
        }
        else{
            if ($user->packages->isNotEmpty())
                return response()->json(['success' => true, 'response' => $user->packages]);
            else
                return response()->json(['success' => false, 'message' => 'Any package']);
        }
    }

    public function transactions(string $call_id)
    {
        $user = User::where('call_id', $call_id)->first();
        if (!$user)
        {
            return response()->json(['success' => false, 'message' => 'Utilisateur indisponible'], 404);
        }
        else{
            if ($user->transactions->isNotEmpty())
                return response()->json(['success' => true, 'response' => $user->transactions]);
            else
                return response()->json(['success' => false, 'message' => 'Any transaction']);
        }
    }

    public function updateConnection(string $call_id)
    {
        $user = User::where('call_id', $call_id)->first();
        if (!$user)
        {
            return response()->json(['success' => false, 'message' => 'Utilisateur indisponible'], 404);
        }
        else{
            $user->updateLastConnexion();
            $user->save();
            return response()->json(['success' => true, 'message' => 'Connexion mise à jour']);
        }
    }

}
