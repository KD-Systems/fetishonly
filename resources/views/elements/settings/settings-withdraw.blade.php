<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-12">
                <label for="method">Select Method</label>
                <select name="method" id="method" class="form-control">
                    <option value="bank">Bank Account</option>
                    <option value="paypal">PayPal</option>
                </select>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-12 w-100" id="bank">
                <form action="{{ route('my.settings.withdraw.save', ['type' => 'bank']) }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="bank">
                    <div class="form-group w-100">
                        <label id="" for="withdrawal-payment-identifier">Bank Name</label>
                        <input class="form-control" type="text" id="bank_name" name="bank_name" required>
                    </div>
                    <div class="form-group w-100">
                        <label id="" for="withdrawal-payment-identifier">Account Name</label>
                        <input class="form-control" type="text" id="account_name" name="account_name" required>
                    </div>
                    <div class="form-group w-100">
                        <label id="" for="withdrawal-payment-identifier">Account Number/IBAN</label>
                        <input class="form-control" type="text" id="account_number" name="account_number" required>
                    </div>
                    <div class="form-group w-100">
                        <label id="" for="withdrawal-payment-identifier">SWIFT/BIC Code</label>
                        <input class="form-control" type="text" id="swift_code" name="swift_code" required>
                    </div>

                    <button class="btn btn-primary btn-block rounded mr-0 withdrawal-continue-btn" type="submit">{{__('Submit')}}</button>
                </form>
            </div>
            <div class="col-12 w-100" id="paypal" style="display: none">
                <form action="{{ route('my.settings.withdraw.save', ['type' => 'paypal']) }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="paypal">
                    <div class="form-group w-100">
                        <label id="" for="withdrawal-payment-identifier">PayPal Email</label>
                        <input class="form-control" type="email" id="paypal_email" name="paypal_email" required>
                    </div>

                    <button class="btn btn-primary btn-block rounded mr-0 withdrawal-continue-btn" type="submit">{{__('Submit')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>
<hr>
<br>
<br>
<div class="row">
    <div class="col-12">
        <h3>Available Withdraw methods</h3>
        <hr>
    </div>
    <div class="col-12 w-100">
        @forelse ($withdraw_methods as $item)

        @if ($item->type == 'bank')
            <div style="display: flex; border: 1px solid #999; padding: 20px; border-radius: 10px; align-items: center; margin-bottom: 10px;">
                <div style="width: 80%;">
                    <h4 class="m-0 p-0">Bank Account</h4>
                    <p class="m-0 p-0">Account Number/IBAN: {{ $item->account_number }}</p>
                    <p class="m-0 p-0">SWIFT/BIC Code: {{ $item->swift_code }}</p>
                    <p class="m-0 p-0">Account Name: {{ $item->account_name }}</p>
                    <p class="m-0 p-0">Bank Name: {{ $item->bank_name }}</p>
                </div>
                <div style="width: 20%;">
                    <div class="btn-group" style="float: right">
                        <button class="btn btn-sm btn-primary update_withdraw_btn" data-target="#update_withdraw_modal" data-toggle="modal" data-type="bank" data-account-name="{{ $item->account_name }}" data-account-number="{{ $item->account_number }}" data-code="{{ $item->swift_code }}" data-bank-name="{{ $item->bank_name }}" data-id="{{ $item->id }}">Edit</button>
                        <a href="{{ route('my.settings.withdraw.remove', ['id' => $item->id]) }}" class="btn btn-sm btn-danger">Remove</a>
                    </div>
                </div>
            </div>
        @else
            <div style="display: flex; border: 1px solid #999; padding: 20px; border-radius: 10px; align-items: center; margin-bottom: 10px;">
                <div style="width: 80%;">
                    <h4 class="m-0 p-0">PayPal</h4>
                    <p class="m-0 p-0">PayPal Email	: {{ $item->paypal_email }}</p>
                </div>
                <div style="width: 20%;">
                    <div class="btn-group" style="float: right">
                        <button class="btn btn-sm btn-primary update_withdraw_btn" data-target="#update_withdraw_modal" data-toggle="modal" data-type="paypal" data-email="{{ $item->paypal_email }}" data-id="{{ $item->id }}">Edit</button>
                        <a href="{{ route('my.settings.withdraw.remove', ['id' => $item->id]) }}" class="btn btn-sm btn-danger">Remove</a>
                    </div>
                </div>
            </div>
        @endif
        @empty
            <p>No Withdraw method available</p>
        @endforelse
    </div>
</div>
