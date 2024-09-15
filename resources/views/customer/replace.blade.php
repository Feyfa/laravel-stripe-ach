@extends('main')

@section('container')
    <div class="flex justify-betweens gap-10 w-full px-10">
        <form action="{{ route('replace-customer-stripe') }}" method="POST" class="w-1/2 border border-neutral-500 rounded shadow-md p-4 gap-2 flex flex-col justify-center shadow-md h-[530px]">
            @csrf
            <div class="text-center">
                <h1 class="text-xl font-medium">Replace Card Stripe ACH</h1>
            </div>
            <div class="flex flex-col gap-1">
                <label for="customer_card_id">Customer Card ID</label>
                <input type="text" required name="customer_card_id" id="customer_card_id" class="input border border-neutral-500 rounded px-2 py-1 outline-none" placeholder="Customer Card ID">
            </div>
            <div class="flex flex-col gap-1">
                <label for="old_bank_account_id">Old Bank Account ID</label>
                <input type="text" required name="old_bank_account_id" id="old_bank_account_id" class="input border border-neutral-500 rounded px-2 py-1 outline-none" placeholder="Old Bank Account ID">
            </div>
            <div class="flex flex-col gap-1">
                <label for="new_account_holder_name">New Account Holder Name</label>
                <input type="text" required name="new_account_holder_name" id="new_account_holder_name" class="input border border-neutral-500 rounded px-2 py-1 outline-none" placeholder="New Account Holder Name">
            </div>
            <div class="flex flex-col gap-1">
                <label for="new_account_holder_type">New Account Holder Type</label>
                <select required name="new_account_holder_type" id="new_account_holder_type" class="input border border-neutral-500 rounded px-2 py-1 outline-none">
                    <option value="individual">individual</option>
                    <option value="company">company</option>
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label for="new_routing_number">New Routing Number</label>
                <input type="text" required name="new_routing_number" id="new_routing_number" class="input border border-neutral-500 rounded px-2 py-1 outline-none" placeholder="New Routing Number">
            </div>
            <div class="flex flex-col gap-1">
                <label for="new_account_number">New Account Number</label>
                <input type="text" required name="new_account_number" id="new_account_number" class="input border border-neutral-500 rounded px-2 py-1 outline-none" placeholder="New Account Number">
            </div>
            <div class="mt-2">
                <button type="submit" name="replace_submit" class="border border-neutral-500 rounded shadow bg-blue-500 text-white w-full px-2 py-1 hover:opacity-[0.95]">Replace Card</button>
            </div>
        </form>
        <div class="w-1/2 border border-neutral-500 rounded shadow-md px-4 pb-4 pt-8 gap-2 flex flex-col shadow-md h-[530px]">
            <div class="text-center">
                <h1 class="text-xl font-medium">Result</h1>
            </div>

            @if (session('check'))
                @isset(session('check')['new_customer_card_id'])  
                    <div class="flex flex-col gap-1">
                        <label>Customer ID</label>
                        <input readonly type="text"class="input border border-neutral-500 rounded px-2 py-1 outline-none" value="{{ session('check')['new_customer_card_id'] }}">
                    </div>
                @endisset

                @isset(session('check')['new_bank_account_id'])  
                    <div class="flex flex-col gap-1">
                        <label>New Bank Account ID</label>
                        <input readonly type="text" class="input border border-neutral-500 rounded px-2 py-1 outline-none" value="{{ session('check')['new_bank_account_id'] }}">
                    </div>
                @endisset

                @isset(session('check')['error'])  
                    <div class="flex flex-col gap-1">
                        <label>Error</label>
                        <textarea readonly type="text" class="input border border-red-500 rounded px-2 py-1 outline-none w-full" rows="5">{{ session('check')['message'] }}</textarea>
                    </div>
                @endisset
            @endif
        </div>
    </div>
@endsection