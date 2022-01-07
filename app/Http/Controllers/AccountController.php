<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Validator;
use Input;
use Redirect;

class AccountController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(Input::all(), [
            'username' => 'required|max:30',
        ]);
        if ($validator->fails()) {
            return Redirect::to('/')
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            $username =  Input::get('username');
            $account = Account::firstOrNew([
                'username' => $username
            ]);
            $account->save();

            return Redirect::to('/account/show/'.$username);
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        $account = Account::where('username', $username)->firstOrFail();
        return view('account/show', compact($account));
    }
}
