<?php

namespace App\Http\Controllers;

use App\WithdrawMethod;
use Illuminate\Http\Request;

class WithdrawMethodController extends Controller
{
    public function store(Request $request, $type) {
        if($type == 'bank') {
            $request->validate([
                'type' => 'required',
                'bank_name' => 'required',
                'account_name' => 'required',
                'account_number' => 'required',
                'swift_code' => 'required',
            ]);

            $data = [
                'type' => $request->type,
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'swift_code' => $request->swift_code,
                'user_id' => auth()->user()->id
            ];

            WithdrawMethod::create($data);
        } else {
            $request->validate([
                'type' => 'required',
                'paypal_email' => 'required|email'
            ]);

            $data = [
                'type' => $request->type,
                'paypal_email' => $request->paypal_email,
                'user_id' => auth()->user()->id
            ];

            WithdrawMethod::create($data);
        }

        return back()->with('success', 'Withdraw method created success!');
    }

    public function update(Request $request, $type) {

        if($type == 'bank') {
            $request->validate([
                'id' => 'required',
                'type' => 'required',
                'bank_name' => 'required',
                'account_name' => 'required',
                'account_number' => 'required',
                'swift_code' => 'required',
            ]);

            $data = [
                'type' => $request->type,
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'swift_code' => $request->swift_code,
                'user_id' => auth()->user()->id
            ];

            WithdrawMethod::where('id', $request->id)->update($data);
        } else {
            $request->validate([
                'id' => 'required',
                'type' => 'required',
                'paypal_email' => 'required|email'
            ]);

            $data = [
                'type' => $request->type,
                'paypal_email' => $request->paypal_email,
                'user_id' => auth()->user()->id
            ];

            WithdrawMethod::where('id', $request->id)->update($data);
        }

        return back()->with('success', 'Withdraw method created success!');
    }

    public function remove($id) {
        $method = WithdrawMethod::where('user_id', auth()->user()->id)->where('id', $id)->firstOrFail();
        $method->delete();

        return back();
    }
}
