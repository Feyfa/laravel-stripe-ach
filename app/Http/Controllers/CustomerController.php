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

            // /* JIKA BELUM DI BUAT CUSTOMER NYA */
            // // $customer = $stripe->customers->create([
            // //     "source" => $token_id,
            // //     "description" => $account_holder_name
            // // ]);

            
            // $customer = $stripe->customers->create([
            //     "source" => $token_id,
            //     "description" => $account_holder_name
            // ],['stripe_account' => 'acct_1Q0u2oRr4Oh7GNUV']);

            // $customer_id = $customer->id;
            // /* JIKA BELUM DI BUAT CUSTOMER NYA */

            /* JIKA SUDAH DIBUAT CUSTOMER NYA */
            // $source = $stripe->customers->createSource(
            //     'cus_QsBU5fypJxyY0q',
            //     ['source' => $token_id]
            // );


            $source = $stripe->customers->createSource(
                'cus_QsfXxmGZ4YWgQN',
                ['source' => $token_id],
                ['stripe_account' => 'acct_1Q0u2oRr4Oh7GNUV']
            );
            /* JIKA SUDAH DIBUAT CUSTOMER NYA */

            Log::info('',[
                'customer' => $customer ?? "",
                'customer_id' => $customer_id ?? "",
            ]);

            return redirect('/')->with('check' ?? "", [
                'customer_id' => $customer_id ?? "",
                'bank_account_id' => $bank_account_id ?? "",
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
            
            /* TANPA STRIPE CONNECT */
            // $bank_account = $stripe->customers->retrieveSource(
            //     $customer_card_id,
            //     $bank_account_id
            // );
            /* TANPA STRIPE CONNECT */

            /* PAKAI STRIPE CONNECT */
            $bank_account = $stripe->customers->retrieveSource(
                $customer_card_id,
                $bank_account_id,
                [],
                ['stripe_account' => 'acct_1Q0u2oRr4Oh7GNUV']
            );
            /* PAKAI STRIPE CONNECT */
            
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
            /* TANPA STRIPE ACCOUNT */
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $amount,
                'currency' => 'usd',
                'payment_method_types' => ['us_bank_account'], // Menggunakan ACH
                'customer' => $customer_card_id, // Customer ID dari session
                'payment_method' => $bank_account_id, // Bank Account ID dari session
                'confirm' => true, // Mengonfirmasi dan memulai pembayaran
                'off_session' => false, // Pembayaran dilakukan saat sesi pengguna aktif
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
            /* TANPA STRIPE ACCOUNT */

            /* PAKAI STRIPE ACCOUNT */
            // $paymentIntent = $stripe->paymentIntents->create([
            //     'amount' => $amount,
            //     'currency' => 'usd',
            //     'payment_method_types' => ['us_bank_account'], // Menggunakan ACH
            //     'customer' => $customer_card_id, // Customer ID dari session
            //     'payment_method' => $bank_account_id, // Bank Account ID dari session
            //     'confirm' => true, // Mengonfirmasi dan memulai pembayaran
            //     'off_session' => false, // Pembayaran dilakukan saat sesi pengguna aktif
            //     'mandate_data' => [
            //         'customer_acceptance' => [
            //             'type' => 'online',
            //             'online' => [
            //                 'ip_address' => $ipAddress, // Alamat IP pengguna
            //                 'user_agent' => $userAgent, // User agent pengguna
            //             ],
            //         ],
            //     ],
            // ],['stripe_account' => 'acct_1Q0u2oRr4Oh7GNUV']);
            /* PAKAI STRIPE ACCOUNT */

        
            Log::info('', [
                'paymentIntent' => $paymentIntent
            ]);

            $paymentIntentId = $paymentIntent->id;

            while(true) {
                try {
                    sleep(4);

                    Log::info('retreive');

                    /* TIDAK PAKAI STRIPE ACCOUNT */
                    $paymentIntent  = $stripe->paymentIntents->retrieve($paymentIntentId);
                    /* TIDAK PAKAI STRIPE ACCOUNT */
                    
                    /* PAKAI STRIPE ACCOUNT */
                    // $paymentIntent  = $stripe->paymentIntents->retrieve(
                    //     $paymentIntentId,
                    //     [],
                    //     ['stripe_account' => 'acct_1Q0u2oRr4Oh7GNUV']
                    // );
                    /* PAKAI STRIPE ACCOUNT */
                    
                    if($paymentIntent->status != 'processing') {
                        Log::info("", [
                            'paymentIntent' => $paymentIntent
                        ]);

                        break;
                    }
                } catch (\Exception $e) {
                    Log::info("", [
                        'error' => $e->getMessage()
                    ]);

                    return redirect('/charge')->with('check', [
                        'error' => true,
                        'message' => json_encode($e->getMessage()),
                    ]);

                    break; exit; die();
                }
            }

            if($paymentIntent->status == 'requires_payment_method') {
                return redirect('/charge')->with('check', [
                    'error' => true,
                    'message' => $paymentIntent->last_payment_error->message,
                ]);
            } else if($paymentIntent->status == 'succeeded') {
                return redirect('/charge')->with('check', [
                    'success' => true,
                    'message' => "success charge {$request->amount} amount",
                ]);
            } else {
                return redirect('/charge')->with('check', [
                    'error' => true,
                    'message' => "Something Error",
                ]);
            }
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

    public function replaceView()
    {
        return view('customer.replace');
    }

    public function replaceCustomerStripe(Request $request)
    {
        $request->validate([
            'customer_card_id' => ['required'],
            'old_bank_account_id' => ['required'],
            'new_account_holder_name' => ['required'],
            'new_account_holder_type' => ['required'],
            'new_routing_number' => ['required'],
            'new_account_number' => ['required'],
        ]);

        $customer_card_id = $request->customer_card_id;
        $old_bank_account_id = $request->old_bank_account_id;
        $new_account_holder_name = $request->new_account_holder_name;
        $new_account_holder_type = $request->new_account_holder_type;
        $new_routing_number = $request->new_routing_number;
        $new_account_number = $request->new_account_number;

        Log::info([
            'customer_card_id' => $request->customer_card_id,
            'old_bank_account_id' => $request->old_bank_account_id,
            'new_account_holder_name' => $request->new_account_holder_name,
            'new_account_holder_type' => $request->new_account_holder_type,
            'new_routing_number' => $request->new_routing_number,
            'new_account_number' => $request->new_account_number,
        ]);

        $stripe = new StripeClient(config('stripe.secret_key'));

        try {
            /* DELETE OLD BANK ACCOUNT */
            $deleteSource = $stripe->customers->deleteSource(
                $customer_card_id,
                $old_bank_account_id
            );
            Log::info("", [
                'deleteSource' => $deleteSource
            ]);
            /* DELETE OLD BANK ACCOUNT */   

            /* CREATE NEW TOKEN ADD NEW BANK ACCOUNT ID */
            $token = $stripe->tokens->create([
                "bank_account" => [
                    "country" => "US",
                    "currency" => "USD",
                    "account_holder_name" => $new_account_holder_name,
                    "account_holder_type" => $new_account_holder_type,
                    "routing_number" => $new_routing_number,
                    "account_number" => $new_account_number
                ]
            ]);
            $token_id = $token->id;
            $bank_account_id = $token->bank_account->id;

            Log::info("", [
                'token' => $token,
                'token_id',
                'bank_account_id'
            ]);

            $customerCreateSorce = $stripe->customers->createSource(
                $customer_card_id,
                ['source' => $token_id]
            );

            Log::info("", [
                'customerCreateSorce' => $customerCreateSorce,
                'customer_id' => $customerCreateSorce->customer,
                'customer_card_id' => $customer_card_id
            ]);
            /* CREATE NEW TOKEN ADD NEW BANK ACCOUNT ID */

            return redirect('/replace')->with('check', [
                'new_customer_card_id' => $customer_card_id,
                'new_bank_account_id' => $bank_account_id
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

    public function connectedAccount(Request $request)
    {
        $stripe = new StripeClient(config('stripe.secret_key'));
        
        // create connected accounts
        // $account = $stripe->accounts->create([
        //     'country' => 'US',
        //     'email' => 'user@example.com',
        //     'controller' => [
        //         'fees' => ['payer' => 'application'],
        //         'losses' => ['payments' => 'application'],
        //         'stripe_dashboard' => ['type' => 'express'],
        //     ],
        // ]);
        // $account = $stripe->accounts->create([
        //     'country' => 'US',
        //     'email' => 'user4@example.com',
        //     'controller' => [
        //         'fees' => ['payer' => 'application'],
        //         'losses' => ['payments' => 'application'],
        //         'stripe_dashboard' => ['type' => 'express'],
        //     ],
        //     'capabilities' => [
        //         'card_payments' => ['requested' => true],
        //         'transfers' => ['requested' => true],
        //     ],
        // ]);

        // Log::info('', [
        //     'account' => $account
        // ]);

        // return response()->json([
        //     'account' => $account
        // ]);

        $id = 'acct_1Q0u2oRr4Oh7GNUV';

        // create account links
        $link = $stripe->accountLinks->create([
            'account' => $id,
            'refresh_url' => 'http://stripeach.com/',
            'return_url' => 'http://stripeach.com/',
            'type' => 'account_onboarding',
        ]);

        Log::info('', [
            'link' => $link
        ]);

        return response()->json([
            'link' => $link
        ]);
    }

    public function deleteConnectedAccount(Request $request)
    {
        $stripe = new StripeClient(config('stripe.secret_key'));
        $stripe->accounts->delete('acct_1Q0tioRuY4jwC8iu', []);
    }
}
