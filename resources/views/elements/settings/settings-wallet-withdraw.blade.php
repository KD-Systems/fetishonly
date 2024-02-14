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
        @forelse ($withdraw_methods as $item)
        <div class="d-flex flex-row w-100">

                @if ($item->type == 'bank')
                    <div class="select_method" style="display: flex; border: 1px solid #999; padding: 20px; border-radius: 10px; align-items: center; margin-bottom: 10px; width: 100%;">
                        <div style="width: 80%;">
                            <h4 class="m-0 p-0">Bank Account</h4>
                            <p class="m-0 p-0">Account Number/IBAN: {{ $item->account_number }}</p>
                            <p class="m-0 p-0">SWIFT/BIC Code: {{ $item->swift_code }}</p>
                            <p class="m-0 p-0">Account Name: {{ $item->account_name }}</p>
                            <p class="m-0 p-0">Bank Name: {{ $item->bank_name }}</p>
                        </div>
                        <div style="width: 20%;">
                            <div class="btn-group" style="float: right">
                                <input class="method" type="radio" value="{{ $item->id }}" name="method">
                            </div>
                        </div>
                    </div>
                @else
                    <div class="select_method" style="display: flex; border: 1px solid #999; padding: 20px; border-radius: 10px; align-items: center; margin-bottom: 10px; width: 100%;">
                        <div style="width: 80%;">
                            <h4 class="m-0 p-0">PayPal</h4>
                            <p class="m-0 p-0">PayPal Email	: {{ $item->paypal_email }}</p>
                        </div>
                        <div style="width: 20%;">
                            <div class="btn-group" style="float: right">
                                <input class="method" type="radio" value="{{ $item->id }}" name="method">
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @empty
            <div class="d-flex flex-row w-100">
                    <p>No Withdraw method available</p>
            </div>
            @endforelse
    </div>

    <div class="form-group w-100">
        <label for="withdrawal-message">{{__('Message (Optional)')}}</label>
        <textarea placeholder="{{__('Bank account, notes, etc')}}" class="form-control" id="withdrawal-message" rows="2"></textarea>
        <span class="invalid-feedback" role="alert">
            {{__('Please add your withdrawal notes: EG: Paypal or Bank account.')}}
        </span>
    </div>

    <div class="payment-error error text-danger d-none mt-3">{{__('Add all required info')}}</div>
    <button class="btn btn-primary btn-block rounded mr-0 withdrawal-continue-btn" type="submit">{{__('Request withdrawal')}}</button>
</div>

<br>
<hr>
<br>

<div class="row">
    <div class="col-12 w-100">
        <h4>Withdraw history</h4>
        <hr>
    </div>
    <div class="col-12 w-100">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($withdraws as $item)
                    <tr>
                        <td>{{ now()->create($item->created_at)->toFormattedDateString() }}</td>
                        <td>{{ $item->payment_method ? ($item->payment_method == 'bank' ? 'Bank Account' : 'PayPal') : 'Bank Account' }}</td>
                        <td>
                            @if ($item->payment_method == 'bank')
                                <p class="m-0 p-0">{{ $item->account_number }}</p>
                                <p class="m-0 p-0">{{ $item->account_name }}</p>
                                <p class="m-0 p-0">{{ $item->bank_name }}</p>
                            @else
                                <p class="m-0 p-0">{{ $item->paypal_email }}</p>
                            @endif
                        </td>
                        <td>
                            {{ $item->status }}
                        </td>
                        <td>{{ $item->amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-12 100">
        {!! $withdraws->links() !!}
    </div>
</div>
