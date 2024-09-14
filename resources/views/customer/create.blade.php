@extends('main')

@section('container')
    <div class="flex justify-betweens gap-10 w-full px-10">
        <form action="{{ route('create-customer-stripe') }}" method="POST" class="w-1/2 border border-neutral-500 rounded shadow-md p-4 gap-2 flex flex-col justify-center shadow-md h-[420px]">
            @csrf
            <div class="text-center">
                <h1 class="text-xl font-medium">Create Card Stripe ACH</h1>
            </div>
            <div class="flex flex-col gap-1">
                <label for="account_holder_name">Account Holder Name</label>
                <input type="text" required name="account_holder_name" id="account_holder_name" class="input border border-neutral-500 rounded px-2 py-1 outline-none" placeholder="Account Holder Name">
            </div>
            <div class="flex flex-col gap-1">
                <label for="account_holder_type">Account Holder Type</label>
                <select required name="account_holder_type" id="account_holder_type" class="input border border-neutral-500 rounded px-2 py-1 outline-none">
                    <option value="individual">individual</option>
                    <option value="company">company</option>
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label for="routing_number">Routing Number</label>
                <input type="text" required name="routing_number" id="routing_number" class="input border border-neutral-500 rounded px-2 py-1 outline-none" placeholder="Routing Number">
            </div>
            <div class="flex flex-col gap-1">
                <label for="account_number">Account Number</label>
                <input type="text" required name="account_number" id="account_number" class="input border border-neutral-500 rounded px-2 py-1 outline-none" placeholder="Account Number">
            </div>
            <div class="mt-2">
                <button type="submit" name="create_submit" class="border border-neutral-500 rounded shadow bg-blue-500 text-white w-full px-2 py-1 hover:opacity-[0.95]">Create Card</button>
            </div>
        </form>
        <div class="w-1/2 border border-neutral-500 rounded shadow-md px-4 pb-4 pt-8 gap-2 flex flex-col shadow-md h-[420px]">
            <div class="text-center">
                <h1 class="text-xl font-medium">Result</h1>
            </div>

            @if (session('check'))
                @isset(session('check')['customer_id'])  
                    <div class="flex flex-col gap-1">
                        <label>Customer ID</label>
                        <input readonly type="text"class="input border border-neutral-500 rounded px-2 py-1 outline-none" value="{{ session('check')['customer_id'] }}">
                    </div>
                @endisset

                @isset(session('check')['bank_account_id'])  
                    <div class="flex flex-col gap-1">
                        <label>Bank Account ID</label>
                        <input readonly type="text" class="input border border-neutral-500 rounded px-2 py-1 outline-none" value="{{ session('check')['bank_account_id'] }}">
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