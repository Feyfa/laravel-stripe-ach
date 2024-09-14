<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\StripeClient;

class CustomerController extends Controller
{
    public function createView()
    {
        return view('customer.create');
    }

    public function createCustomerStripe(Request $request)
    {
        $request->validate([
            'account_holder_name' => ['required'],
            'account_holder_type' => ['required'],
            'routing_number' => ['required'],
            'account_number' => ['required'],
        ]);

        $account_holder_name = $request->account_holder_name;
        $account_holder_type = $request->account_holder_type;
        $routing_number = $request->routing_number;
        $account_number = $request->account_number;

        Log::info([
            'account_holder_name' => $account_holder_name,
            'account_holder_type' => $account_holder_type,
            'routing_number' => $routing_number,
            'account_number' => $account_number,
        ]);

        $stripe = new StripeClient(config('stripe.secret_key'));
        
        try {
            $token = $stripe->tokens->create([
                "bank_account" => [
                    "country" => "US",
                    "currency" => "USD",
                    "account_holder_name" => $account_holder_name,
                    "account_holder_type" => $account_holder_type,
                    "routing_number" => $routing_number,
                    "account_number" => $account_number
                ]
            ]);
            $token_id = $token->id;
            $bank_account_id = $token->bank_account->id;

            Log::info('',[
                'token' => $token,
                'token_id' => $token_id,
                'bank_account_id' => $bank_account_id
            ]);

            $customer = $stripe->customers->create([
                "source" => $token_id,
                "description" => $account_holder_name
            ]);
            $customer_id = $customer->id;

            Log::info('',[
                'customer' => $customer,
                'customer_id' => $customer_id
            ]);

            return redirect('/')->with('check', [
                'customer_id' => $customer_id,
                'bank_account_id' => $bank_account_id
            ]);      
        } catch (\Exception $e) {
            Log::error([
                'error' => $e->getMessage()
            ]);

            return redirect('/')->with('check', [
                'error' => true,
                'message' => json_encode($e->getMessage())
            ]);
        }
    }

    public function verifyView()
    {
        return view('customer.verify');
    }

    public function verifyCustomerStripe(Request $request)
    {
        $request->validate([
            'customer_card_id' => ['required'],
            'bank_account_id' => ['required'],
            'amount1' => ['required'],
            'amount2' => ['required'],
        ]);

        $customer_card_id = $request->customer_card_id;
        $bank_account_id = $request->bank_account_id;
        $amount1 = round($request->amount1 * 100, 2);
        $amount2 = round($request->amount2 * 100, 2);

        $stripe = new StripeClient(config('stripe.secret_key'));

        try {
            Log::info([
                'ammount1' => $amount1,
                'ammount2' => $amount2,
                'customer_card_id' => $customer_card_id,
                'bank_account_id' => $bank_account_id,
            ]);
    
            $bank_account = $stripe->customers->retrieveSource(
                $customer_card_id,
                $bank_account_id
            );
            
            Log::info('', [
                'ke' => '1',
                'bank_account_id' => $bank_account_id
            ]);
            
            $bank_account->verify(['amounts' => [$amount1, $amount2]]);

            Log::info('', [
                'ke' => '2',
                'bank_account_id' => $bank_account_id
            ]);
            
            return redirect('/verify')->with('check', [
                'success' => true,
                'message' => 'success verify'
            ]);  
        } catch (\Exception $e) {
            Log::error([
                'error' => $e->getMessage()
            ]);

            return redirect('/verify')->with('check', [
                'error' => true,
                'message' => json_encode($e->getMessage())
            ]);
        }
    }
    
    public function chargeView()
    {
        return view('customer.charge');
    }

    public function chargeCustomerStripe(Request $request)
    {
        $request->validate([
            'customer_card_id' => ['required'],
            'bank_account_id' => ['required'],
            'amount' => ['required'],
        ]);

        $customer_card_id = $request->customer_card_id;
        $bank_account_id = $request->bank_account_id;
        $amount = round($request->amount * 100, 2);

        $ipAddress = $request->ip(); // Mendapatkan IP pengguna
        $userAgent = $request->header('User-Agent'); // Mendapatkan user agent pengguna
        
        $stripe = new StripeClient(config('stripe.secret_key'));

        try {
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $amount,
                'currency' => 'usd',
                'payment_method_types' => ['us_bank_account'], // Menggunakan ACH
                'customer' => $customer_card_id, // Customer ID dari session
                'payment_method' => $bank_account_id, // Bank Account ID dari session
                'confirm' => true, // Mengonfirmasi dan memulai pembayaran
                'off_session' => true, // Untuk pembayaran di luar sesi pengguna
                'mandate_data' => [
                    'customer_acceptance' => [
                        'type' => 'online',
                        'online' => [
                            'ip_address' => $ipAddress, // Alamat IP pengguna
                            'user_agent' => $userAgent, // User agent pengguna
                        ],
                    ],
                ],
            ]);
        
            Log::info('', [
                'paymentIntent' => $paymentIntent
            ]);

            return redirect('/charge')->with('check', [
                'success' => true,
                'message' => "success charge {$request->amount} amount",
            ]);
        
        } catch (\Exception $e) {
            Log::error([
                'error' => $e->getMessage()
            ]);

            return redirect('/charge')->with('check', [
                'error' => true,
                'message' => json_encode($e->getMessage()),
            ]);
        }
    }
}
