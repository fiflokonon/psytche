<?php

namespace App\Jobs;

use App\Events\PaymentCallbackEvent;
use App\Models\Package;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class PaymentVerificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $call_id;
    public $transaction_token;
    public $attempt;

    /**
     * Create a new job instance.
     *
     * @param string $call_id
     * @param string $transaction_token
     * @param int $attempt
     */
    public function __construct(string $call_id, string $transaction_token, int $attempt)
    {
        $this->call_id = $call_id;
        $this->transaction_token = $transaction_token;
        $this->attempt = $attempt;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Utilisez $this->call_id, $this->transaction_token et $this->attempt
        $transactionStatus = $this->fetchInvoiceStatus($this->transaction_token);

        if ($transactionStatus['success'] && $transactionStatus['status'] === 'completed') {
            // Mettez à jour le statut de la transaction et activez le forfait
            $transaction = Transaction::where('token', $this->transaction_token)->first();
            if (!$transaction) {
                Log::error("Transaction with token $this->transaction_token not found.");
                event(new PaymentCallbackEvent($this->call_id, false));
                return;
            }

            $transaction->payment_status = 'completed';
            $transaction->payment_details = json_encode($transactionStatus);
            $transaction->save();

            $user = $transaction->user;
            $custom = json_decode($transaction->custom_data);
            $package_id = $custom->package_id;
            $package = Package::find($package_id);

            $user->deactivateAllPackages();
            $user->packages()->attach($package, [
                'bought_at' => now(),
                'remaining_time' => $package->duration,
                'status' => true,
            ]);

            // Diffuser l'événement PaymentCallbackEvent avec succès
            event(new PaymentCallbackEvent($this->call_id, true));
        } else {
            // Diffuser l'événement PaymentCallbackEvent en échec
            event(new PaymentCallbackEvent($this->call_id, false));

            // Si les conditions ne sont pas remplies, planifiez un nouveau job avec une nouvelle tentative
            if ($this->attempt < 10) {
                $nextAttempt = $this->attempt + 1;
                $nextJob = new self($this->call_id, $this->transaction_token, $nextAttempt);
                Queue::later(now()->addSeconds(30), $nextJob);
            }
        }
    }

    public function fetchInvoiceStatus($invoiceToken) {
        $apiKey = env('PAYPLUS_API_KEY');
        $accessToken = env('PAYPLUS_TOKEN');
        $url = "https://app.payplus.africa/pay/v01/redirect/checkout-invoice/confirm/?invoiceToken={$invoiceToken}";
        $response = Http::withHeaders([
            'Apikey' => $apiKey,
            'Authorization' => "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZF9hcHAiOiI5NzYiLCJpZF9hYm9ubmUiOjc4NTcsImRhdGVjcmVhdGlvbl9hcHAiOiIyMDIyLTA3LTE5IDExOjI2OjUwIn0.2USDGyfTAS-fchV5bimOShq95cjH_I2kKTWSDblQgCI"
        ])->get($url);
        #dd($response);
        if ($response->successful() && $response->header('content-type') === 'application/json') {
            $responseData = $response->json();
            if ($responseData['response_code'] === '00') {
                return [
                    'success' => true,
                    'status' => $responseData['status'],
                    'token' => $responseData['token'],
                    'response' => $responseData['response_text']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $responseData['response_text'],
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'La requête a échoué. Veuillez réessayer ultérieurement.',
            ];
        }
    }
}
