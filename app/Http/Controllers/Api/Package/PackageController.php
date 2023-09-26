<?php

namespace App\Http\Controllers\Api\Package;

use App\Events\PaymentCallbackEvent;
use App\Http\Controllers\Controller;
use App\Jobs\PaymentVerificationJob;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\TypeTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use StephaneAss\Payplus\Pay\PayPlus;

class PackageController extends Controller
{
    public function packages()
    {
        $packages = Package::where('status', true)->get();
        if ($packages)
            return response()->json(['success' => true, 'response' => $packages]);
        else
            return response()->json(['success' => false, 'message' => 'Pas de forfait disponible'], 404);
    }

    public function initPackage(string $call_id, string $package_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorMessage = $errors->first();
            return response()->json(['success' => false, 'message' => $errorMessage], 400);
        }

        $user = User::where('call_id', $call_id)->first();
        $package = Package::find($package_id);

        if (!$user || !$package) {
            return response()->json(['success' => false, 'message' => 'Utilisateur ou forfait indisponible'], 404);
        } else {
            try {
                $type = TypeTransaction::where('code', 'package')->first();
                // Créer la transaction avec le statut 'initiated'
                $transaction = Transaction::create([
                    'type_transaction_id' => $type->id,
                    'user_id' => $user->id,
                    'amount' => $package->price,
                    'phone' => $request->phone,
                    'package_id' => $package->id,
                    'payment_status' => 'pending',
                    'status' => true,
                    'transaction_status' => 'initiated', // Nouvel attribut pour le statut de la transaction
                ]);

                // Initier le paiement avec l'id de la transaction
                $init_payment = $this->createPayplusInvoiceForPackage($request->phone, $package_id, $package->price, $transaction->id);

                if ($init_payment['success']) {
                    // Mettre à jour les détails de la transaction avec le token et le paiement_details
                    $transaction->token = $init_payment['token'];
                    $transaction->payment_details = json_encode($init_payment);
                    $transaction->save();
                    return response()->json(['success' => true, 'response' => $transaction]);
                } else {
                    return response()->json(['success' => false, 'message' => $init_payment['message']]);
                }
            } catch (\Exception $exception) {
                return response()->json(['success' => false, 'message' => $exception->getMessage()]);
            }
        }
    }

    public function createPayplusInvoiceForPackage($phone, $package_id, $package_price, $transaction_id)
    {
        Log::info('Initialisation de la transaction');
        $co = (new PayPlus())->init();
        $co->addItem("Achat forfait", 1, $package_price, $package_price, "test");
        $total_amount = $package_price;
        $co->setTotalAmount($total_amount);
        $co->setDescription("Achat forfait psytche");
        $co->addCustomData('transaction_id', $transaction_id); // Ajout de l'id de la transaction
        $co->setCustomerNumber($phone); // It must be on this format 22967710659
        $co->setDevise("xof"); // By default, it is already on xof
        $co->setOtp(""); // Contains the otp code of the transaction (only for orange money subscribers, otherwise leave empty).
        $result = $co->launchPaiement();
        $responseData = $result;
        if ($responseData->response_code === '00') {
            return [
                'success' => true,
                'token' => $responseData->token,
                'redirect_url' => $responseData->response_text,
                'description' => $responseData->description,
            ];
        } else {
            return [
                'success' => false,
                'message' => $responseData->description,
            ];
        }
    }


    public function checking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorMessage = $errors->first();
            return response()->json(['success' => false, 'message' => $errorMessage], 400);
        }
        $transaction = Transaction::where('status', true)->where('token', $request->token)->first();
        if (!$transaction) {
            return response()->json(['success' => true, 'message' => 'Transaction indisponible']);
        } elseif ($transaction['payment_status'] == 'completed') {
            return response()->json(['success' => false, 'message' => 'Transaction déjà validée']);
        } else {
            $transactionChecking = $this->fetchInvoiceStatus($request->token);
            #dd($transactionChecking);
            if ($transactionChecking['success'] && $transactionChecking['status'] == 'completed') {
                try {
                    $transaction->payment_status = 'completed';
                    #$transaction->payment_details = json_encode($transactionChecking);
                    #$transaction->save();
                    $user = $transaction->user;
                    $custom = json_decode($transaction->custom_data);
                    $package_id = $custom->package_id;
                    $package = Package::find($package_id);
                    $user->deactivateAllPackages();
                    $user->packages()->attach($package, [
                        'bought_at' => now(),
                        'remaining_time' => $package->duration,
                        'status' => true
                    ]);
                    $transaction->save();
                    return response()->json(['success' => true, 'message' => 'Forfait activé avec succès']);
                } catch (\Exception $exception) {
                    return response()->json(['success' => false, 'message' => $exception->getMessage()]);
                }
            } else {
                return response()->json(['success' => false, 'message' => $transactionChecking['status']]);
            }
        }
    }

    public function verifyPaiementCallback(Request $request)
    {
        $response_code = $request->response_code;
        $custom_data = $request->custom_data;
        #$package_id = $custom_data[0]['valueof_customdata'];
        $transaction_id = $custom_data[1]['valueof_customdata'];
        if ($response_code == 00) {
            $transaction = Transaction::find($transaction_id);
            if ($transaction) {
                Log::info('La transaction existe');
                $transaction->payment_status = 'completed';
                $transaction->transaction_status = 'validated';
                $user = $transaction->user;
                $custom = json_decode($transaction->custom_data);
                $package_id = $custom->package_id;
                $package = Package::find($package_id);
                $user->deactivateAllPackages();
                $user->packages()->attach($package, [
                    'bought_at' => now(),
                    'remaining_time' => $package->duration,
                    'package_status' => true
                ]);
                $transaction->save();
                $call_id = $transaction->user->call_id;
                event(new PaymentCallbackEvent($call_id, true));
            }
        } else {
            $transaction = Transaction::find($transaction_id);
            if ($transaction) {
                $transaction->transaction_status = 'aborted';
                $transaction->payment_status = 'uncompleted';
                $transaction->save();
                $call_id = $transaction->user->call_id;
                event(new PaymentCallbackEvent($call_id, false));
            }
        }
    }

    public function test()
    {
        #dd("test");
        return event(new PaymentCallbackEvent("Test", false));
    }

    public function testo()
    {
        return response()->json(['testo' => 'validé']);
    }
}
