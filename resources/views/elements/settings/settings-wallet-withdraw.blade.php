<div class="d-flex justify-content-between align-items-center mt-3">
    @if(getSetting('payments.withdrawal_allow_fees') && floatval(getSetting('payments.withdrawal_default_fee_percentage')) > 0)
        <div class="d-flex align-items-center">
            @include('elements.icon',['icon'=>'information-circle-outline','variant'=>'small','centered'=>false,'classes'=>'mr-2'])
            <span class="text-left" id="pending-balance" title="{{__("The payouts are manually and it usually take up to 24 hours for a withdrawal to be processed, we will notify you as soon as your request is processed.")}}">
            {{__("A :feeAmount% fee will be applied.",['feeAmount'=>floatval(getSetting('payments.withdrawal_default_fee_percentage'))])}}
        </span>
        </div>
    @else
        <h5></h5>
    @endif
    <div class="d-flex align-items-center">
        @include('elements.icon',['icon'=>'information-circle-outline','variant'=>'small','centered'=>false,'classes'=>'mr-2'])
        <span class="text-right" id="pending-balance" title="{{__("The payouts are manually and it usually take up to 24 hours for a withdrawal to be processed, we will notify you as soon as your request is processed.")}}">
            {{__('Pending balance')}} (<b class="wallet-pending-amount">{{config('app.site.currency_symbol')}}{{number_format(Auth::user()->wallet->pendingBalance, 2, '.', '')}}</b>)
        </span>
    </div>
</div>
<div class="input-group mb-3 mt-3">
    <div class="input-group-prepend">
        <span class="input-group-text" id="amount-label">@include('elements.icon',['icon'=>'cash-outline','variant'=>'medium'])</span>
    </div>
    <input class="form-control"
           placeholder="{{ \App\Providers\PaymentsServiceProvider::getWithdrawalAmountLimitations() }}"
           aria-label="Username"
           aria-describedby="amount-label"
           id="withdrawal-amount"
           type="number"
           min="{{\App\Providers\PaymentsServiceProvider::getWithdrawalMinimumAmount()}}"
           step="1"
           max="{{\App\Providers\PaymentsServiceProvider::getWithdrawalMaximumAmount()}}">
    <div class="invalid-feedback">{{__('Please enter a valid amount')}}</div>
    <div class="input-group mb-3 mt-3">
        <div class="d-flex flex-row w-100">
            <div class="form-group w-100 pr-2">
                <label for="paymentMethod">{{__('Payment method')}}</label>
                <select class="form-control" id="payment-methods" name="payment-methods">
                    {{-- @foreach(\App\Providers\PaymentsServiceProvider::getWithdrawalsAllowedPaymentMethods() as $paymentMethod)
                        <option value="{{$paymentMethod}}">{{$paymentMethod}}</option>
                    @endforeach --}}
                    <option value="bank">Bank Account</option>
                    <option value="paypal">PayPal</option>
                </select>
            </div>
            {{-- <div class="form-group w-50 pl-2">
                <label id="" for="withdrawal-payment-identifier">{{__("Bank account")}}</label>
                <input class="form-control" type="text" id="withdrawal-payment-identifier" name="payment-identifier">
            </div> --}}
        </div>
        <div class="w-100" id="for_bank" style="display: none;">
            <div class="form-group w-100">
                <label id="" for="withdrawal-payment-identifier">Bank Name</label>
                <input class="form-control" type="text" id="bank_name" name="payment-identifier">
            </div>
            <div class="form-group w-100">
                <label id="" for="withdrawal-payment-identifier">Account Name</label>
                <input class="form-control" type="text" id="account_name" name="payment-identifier">
            </div>
            <div class="form-group w-100">
                <label id="" for="withdrawal-payment-identifier">Account Number/IBAN</label>
                <input class="form-control" type="text" id="account_number" name="payment-identifier">
            </div>
            <div class="form-group w-100">
                <label id="" for="withdrawal-payment-identifier">SWIFT/BIC Code</label>
                <input class="form-control" type="text" id="swift_code" name="payment-identifier">
            </div>
        </div>
        <div class="w-100" id="for_paypal" style="display: none;">
            <div class="form-group w-100">
                <label id="" for="withdrawal-payment-identifier">PayPal Email</label>
                <input class="form-control" type="text" id="paypal_email" name="payment-identifier">
            </div>
        </div>
        <div class="form-group w-100">
            <label for="withdrawal-message">{{__('Message (Optional)')}}</label>
            <textarea placeholder="{{__('Bank account, notes, etc')}}" class="form-control" id="withdrawal-message" rows="2"></textarea>
            <span class="invalid-feedback" role="alert">
                {{__('Please add your withdrawal notes: EG: Paypal or Bank account.')}}
            </span>
        </div>
    </div>

    <div class="payment-error error text-danger d-none mt-3">{{__('Add all required info')}}</div>
    <button class="btn btn-primary btn-block rounded mr-0 withdrawal-continue-btn" type="submit">{{__('Request withdrawal')}}</button>
</div>
