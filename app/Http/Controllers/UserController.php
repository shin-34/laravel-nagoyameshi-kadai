<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //index
    public function index()
    {
        $user = Auth::user();
        return view('user.index', compact('user'));
    }



    //edit
    public function edit(User $user)
    {
        // 現在ログイン中のユーザーのIDを取得
        $currentUserId = Auth::id();
        // 受け取ったユーザーのIDと現在のユーザーのIDを比較
        if ($user->id !== $currentUserId) {
            // 一致しない場合は会員情報ページにリダイレクト
            return redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
        }

        return view('user.edit', compact('user'));
    }



    //update
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'kana' => ['required', 'string', 'regex:/\A[ァ-ヴー\s]+\z/u', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'postal_code' => ['required', 'digits:7'],
            'address' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'digits_between:10, 11'],
            'birthday' => ['nullable', 'digits:8'],
            'occupation' => ['nullable', 'string', 'max:255'],
        ]);

        $currentUserId = Auth::id();
        if ($user->id !== $currentUserId) {
            return redirect()->route('user.index');
        }

        $user->name = $request->input('name');
        $user->kana = $request->input('kana');
        $user->email = $request->input('email');
        $user->postal_code = $request->input('postal_code');
        $user->address = $request->input('address');
        $user->phone_number = $request->input('phone_number');
        $user->birthday = $request->input('birthday');
        $user->occupation = $request->input('occupation');
        $user->save();

        return redirect()->route('user.index')->with('flash_message', '会員情報を編集しました。');
    }


}
