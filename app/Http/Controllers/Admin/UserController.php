<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        if ($keyword !== null) {
            $users = User::where('name', 'like', "%{$keyword}%")->orwhere('kana', 'like', "%{$keyword}%")->paginate(15);
        } else {
            $users = User::paginate(15);
        }

        $total = User::count();

        return view('admin.users.index', compact('users', 'keyword', 'total'));
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('users'));
    }

}
