@extends('main')

@section('container')
    <div class="flex justify-betweens gap-10 w-full px-10">
        <form action="{{ route('verify-customer-stripe') }}" method="POST" class="w-1/2 border border-neutral-500 rounded shadow-md p-4 gap-2 flex flex-col shadow-md justify-center shadow-md h-[350px]">
            @csrf
            <div class="text-center">
                <h1 class="text-xl font-medium">Verify Card Stripe ACH</h1>
            </div>
            <div class="flex flex-col gap-1">
                <label for="customer_card_id">Customer Card Id</label>
                <input type="text" required name="customer_card_id" id="customer_card_id" class="input border border-neutral-500 rounded px-2 py-1 outline-none" placeholder="Customer Card Id">
            </div>
            <div class="flex flex-col gap-1">
                <label for="bank_account_id">Bank Account Id</label>
                <input type="text" required name="bank_account_id" id="bank_account_id" class="input border border-neutral-500 rounded px-2 py-1 outline-none" placeholder="Bank Account Id">
            </div>
            <div class="flex justify-center items-center gap-4 w-full">
                <div class="flex flex-col gap-1 w-full">
                    <label for="amount1">Amount 1</label>
                    <input type="text" required name="amount1" id="amount1" class="input border border-neutral-500 rounded px-2 w-full py-1 outline-none" placeholder="Amount 1">
                </div>
                <div class="flex flex-col gap-1 w-full">
                    <label for="amount2">Amount 2</label>
                    <input type="text" required name="amount2" id="amount2" class="input border border-neutral-500 rounded px-2 w-full py-1 outline-none" placeholder="Amount 2">
                </div>
            </div>
            <div class="mt-2">
                <button type="submit" name="verify_submit" class="border border-neutral-500 rounded shadow bg-blue-500 text-white w-full px-2 py-1 hover:opacity-[0.95]">Verify Card</button>
            </div>
        </form>
        <div class="w-1/2 border border-neutral-500 rounded shadow-md px-4 pb-4 pt-8 gap-2 flex flex-col shadow-md h-[350px]">
            <div class="text-center">
                <h1 class="text-xl font-medium">Result</h1>
            </div>

            @if (session('check'))
                @isset(session('check')['success'])  
                    <div class="flex flex-col gap-1">
                        <label>Success</label>
                        <input readonly type="text"class="input border border-neutral-500 rounded px-2 py-1 outline-none" value="{{ session('check')['message'] }}">
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