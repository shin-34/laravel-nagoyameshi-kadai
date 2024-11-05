<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SubscriptionController extends Controller
{
    //create
    public function create()
    {
        $intent = Auth::user()->createSetupIntent();
        return view('subscription.create', compact('intent'));
    }


    //store
    public function store(Request $request)
    {
        $request->user()->newSubscription(
            'premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay'
        )->create($request->paymentMethodId);

        return redirect()->route('user.index')->with('flash_message', '有料プランへの登録が完了しました。');
    }


    //edit
    public function edit() {
        $user = Auth::user();
        $intent = Auth::user()->createSetupIntent();
        return view('subscription.edit', compact('user', 'intent'));
    }


    //update
    public function update(Request $request)
    {
        $request->user()->updateDefaultPaymentMethod($request->paymentMethodId);
        return redirect()->route('user.index')->with('flash_message', 'お支払い方法を変更しました。');
    }


    //cancel
    public function cancel()
    {
        return view('subscription.cancel');
    }


    //destroy
    public function destroy(Request $request)
    {
        $request->user()->subscription('premium_plan')->cancelNow();
        return redirect()->route('user.index')->with('flash_message', '有料プランを解約しました。');
    }
    
}
