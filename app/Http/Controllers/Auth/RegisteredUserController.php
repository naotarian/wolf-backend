<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TemporaryAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Mail\Auth\TemporaryRegisterMail;
use Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Events\MyEvent;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $rules = array();
        $messages = array();
        $rules['name'] = ['required', 'string', 'max:255'];
        $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:' . User::class];
        $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];

        $messages['name.max'] = 'ユーザー名は255文字以内で入力してください。';
        $messages['name.required'] = 'ユーザー名は必須項目です。';
        $messages['name.string'] = 'ユーザー名は文字列で入力してください。';

        $messages['email.string'] = 'メールアドレスは文字列で入力してください。';
        $messages['email.required'] = 'メールアドレスは必須項目です。';
        $messages['email.email'] = 'メールアドレスはアドレス形式で入力してください。';
        $messages['email.unique'] = 'このメールアドレスはすでに使用されています。';

        $messages['password.required'] = 'パスワードは必須項目です。';
        $messages['password.string'] = 'パスワードは文字列で入力してください。';
        $messages['password.confirmed'] = '確認用パスワードと一致しません。';
        $messages['password.min'] = 'パスワードは最低8文字で設定してください。';
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->messages());
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'character_id' => $request->character,
            'password' => Hash::make($request->password),
            'email_verified_at' => Carbon::now()
        ]);

        // event(new Registered($user));

        Auth::login($user);

        return response()->noContent();
    }
    public function temporary_regist(Request $request): Response
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
        ]);
        try {
            DB::transaction(function () use (&$request) {
                TemporaryAccount::where('email', $request->email)->delete();
                $user = TemporaryAccount::create([
                    'email' => $request->email,
                ]);
                Mail::to($user['email'])->send(new TemporaryRegisterMail($user));
            }, 3);
            return response()->noContent();
        } catch (\Exception $e) {
            Log::error($e);
        }
        // $user = TemporaryAccount::create([
        //     'email' => $request->email,
        // ]);

        // event(new Registered($user));

        // Auth::login($user);

    }
    public function token_check(Request $request)
    {
        $token = $request['token'];
        $user = TemporaryAccount::find($token);


        return response()->json($user);
    }

    public function pusher_test(Request $request)
    {
        event(new MyEvent($request['text']));
        return response()->noContent();
    }
}
