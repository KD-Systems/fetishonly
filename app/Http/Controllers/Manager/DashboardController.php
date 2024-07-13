<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Model\ReferralCodeUsage;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $referred = ReferralCodeUsage::with('usedBy')->where('referral_code', auth()->user()->referral_code)->get();
        return view('manager.index', compact('referred'));
    }


}
