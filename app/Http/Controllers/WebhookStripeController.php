<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookStripeController extends Controller
{
    public function stripe(Request $request)
    {
        $type = $request['type'] ?? "";
        $payment_method_types = $request['data']['object']['payment_method_types'][0] ?? "";
        $code_error = $request['data']['object']['last_payment_error']['code'] ?? "";

        // Log::info([
        //     'type' => $type,
        //     'payment_method_types' => $payment_method_types,
        //     'code_error' => $code_error,
        // ]);

        if($type == 'payment_intent.succeeded' && $payment_method_types == 'us_bank_account') 
            $this->payment_intent_succeeded($request);
        else if($type == 'payment_intent.payment_failed' && $payment_method_types == 'us_bank_account' && $code_error != 'charge_exceeds_source_limit') 
            $this->payment_intent_payment_failed($request);
    }

    private function payment_intent_succeeded(Request $request)
    {
        Log::info("========================payment_intent_succeeded========================");
        
        $metadata = $request['data']['object']['metadata'];
        
        Log::info("========================payment_intent_succeeded========================");
    }

    private function payment_intent_payment_failed(Request $request)
    {
        Log::info("========================payment_intent_payment_failed========================");

        $metadata = $request['data']['object']['metadata'];
        $film = $metadata['film'];
        $details = json_decode($metadata['details'], true);
        $attachement = json_decode($metadata['attachement'], true);
        $form = json_decode($metadata['form'], true);

        Log::info([
           'film' => $film, 
           'details' => $details, 
           'attachement' => $attachement, 
           'form' => $form, 
        ]);

        Log::info("========================payment_intent_payment_failed========================");
    }
}
