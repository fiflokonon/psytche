<?php

namespace App\Http\Controllers\Api\Call;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\Parameter;
use App\Models\User;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function Symfony\Component\Translation\t;

class CallController extends Controller
{
    public function newCall(string $call_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'called_id' => ['required', 'string', 'max:255'],
            'language' => ['nullable', 'json'],
            'sex' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'voice_hidden' => ['required', 'boolean']
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorMessage = $errors->first();
            return response()->json(['success' => false, 'message' => $errorMessage], 400);
        }

        $user = User::where('call_id', $call_id)->first();
        $called = User::where('status', true)->where('call_id', $request->called_id)->first();
        if (!$user)
        {
            return response()->json(['success' => false, 'message' => 'Utilisateur indisponible'], 404);
        }
        else{
            if (!$called){
                return response()->json(['success' => false, 'message' => "Lidentifiant d'appel non attribué"], 404);
            }elseif ($user->active_package->isNotEmpty()) {
                $newCall = Call::create([
                    'caller_id' => $user->id,
                    'called_id' => $called->id,
                    'sex' => $request->sex,
                    'languages' => $request->language,
                    'country' => $request->country,
                    'voice_hidden' => $request->voice_hidden
                ]);
                return response()->json(['success' => true, 'response' => $newCall]);
            }
            else{
                return response()->json(['success' => false, 'message' => 'Aucun forfait atif']);
            }
        }
    }

    public function getCalled(string $call_id)
    {
        $user = User::where('call_id', $call_id)->first();
        if (!$user)
        {
            return response()->json(['success' => false, 'message' => 'Utilisateur indisponible'], 404);
        }
        else{
            $called_id = $user->getRandomCallId();
            if ($called_id){
                return response()->json(['success' => true, 'response' => $called_id]);
            }else{
                return response()->json(['success' => false, 'message' => 'Pas de récepteur disponible'], 404);
            }
        }
    }

    public function updateDuration(int $id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'duration' => ['required', 'date_format:H:i:s'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorMessage = $errors->first();
            return response()->json(['success' => false, 'message' => $errorMessage], 400);
        }

        $call = Call::find($id);

        if (!$call) {
            return response()->json(['success' => false, 'message' => 'Appel indisponible'], 404);
        } else {
            $user = $call->caller;
            $called = $call->called;
            $call->duration = $request->duration;
            $timeInSecond = strtotime("1970-01-01 $request->duration UTC");
            $benefit = $this->benefit($timeInSecond);
            $call->benefit = $benefit;
            $call->save();
            $called->balance = $benefit;
            $called->save();
            $activePackage = $user->active_package->first();

            if ($activePackage) {
                $packageDuration = CarbonInterval::createFromFormat('H:i:s', $activePackage->pivot->remaining_time);
                $callDuration = CarbonInterval::createFromFormat('H:i:s', $request->duration);

                if ($callDuration->greaterThan($packageDuration)) {
                    $difference = $callDuration->subtract($packageDuration);
                    if ($packageDuration->greaterThan(CarbonInterval::createFromFormat('H:i:s', '00:00:00'))) {
                        // Mise à jour du remaining_time du forfait actif
                        $activePackage->pivot->remaining_time = '00:00:00';
                        $activePackage->pivot->package_status = false;
                        $activePackage->pivot->save();
                    }
                } else {
                    $difference = $packageDuration->subtract($callDuration);
                    if ($difference->equalTo(CarbonInterval::createFromFormat('H:i:s', '00:00:00'))) {
                        // Mise à jour du remaining_time du forfait actif
                        $activePackage->pivot->remaining_time = '00:00:00';
                        $activePackage->pivot->package_status = false;
                        $activePackage->pivot->save();
                    } else {
                        // Mise à jour du remaining_time du forfait actif
                        $activePackage->pivot->remaining_time = $difference->format('%H:%I:%S');
                        $activePackage->pivot->save();
                    }
                }
            }
            return response()->json(['success' => true, 'response' => $user->callHistory]);
        }
    }

    private function benefit(int $duration)
    {
        // Récupérez le pourcentage pour 60 secondes depuis votre base de données
        $parameter_benefit = Parameter::where('code', 'benefit')->first();
        $benefit_percentage = $parameter_benefit->value;
        $parameter_minute_price = Parameter::where('code', 'minute_price')->first();
        $minute_price = $parameter_minute_price->value;
        $seconds_in_a_minute = 60;
        $benefit = ($duration / $seconds_in_a_minute) * $benefit_percentage * $minute_price;
        return $benefit/100;
    }

}
