<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserForgotPassword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = array(
            'email'    => 'required|email|exists:users', // make sure the email is an actual email
            'password' => 'required' // password can only be alphanumeric and has to be greater than 3 characters
        );

        $messages = [
            'email.required' => 'Bạn phải nhập địa chỉ email.',
            'email.email' => 'Email sai định dạng.',
            'email.exists' => 'Email không tồn tại trên hệ thống.',
            'password.required' => 'Bạn phải nhập mật khẩu.'
        ];
        $request->validate($rules, $messages);

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
            ], true)) {
            //Prevent disable User from logging
            if ('Khóa' == Auth::user()->status->name) {
                Auth::logout();
                return response([
                    'error' => 'Tài khoản đã bị khóa!'
                ], 422);
            } else {
                $user = Auth::user();
                $token = $user->createToken('main')->plainTextToken;
                return response([
                    'user' => $user,
                    'token' => $token
                ]);
            }
        } else {
            return response([
                'error' => 'Email hoặc mật khẩu không đúng!'
            ], 422);
        }
    }

    public function logout()
    {
        $user = Auth::user();
        // Revoke the token that was used to authenticate the current request...
        $user->currentAccessToken()->delete();

        return response([
            'success' => 'Đăng xuất thành công!'
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $rules = array(
            'email'    => 'required|email|exists:users', // make sure the email is an actual email
        );

        $messages = [
            'email.required' => 'Bạn phải nhập địa chỉ email.',
            'email.email' => 'Email sai định dạng.',
            'email.exists' => 'Email không tồn tại trên hệ thống.',
        ];
        $request->validate($rules, $messages);

        //Delete the old password reset request
        DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();

        $token = Str::random(64);
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Notification::route('mail' , $request->email)->notify(new UserForgotPassword($request->email, $token));
    }

    public function resetPassword(Request $request)
    {
        $rules = array(
            'email'    => 'required|email|exists:users', // make sure the email is an actual email
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required',
        );

        $messages = [
            'email.required' => 'Bạn phải nhập địa chỉ email.',
            'email.email' => 'Email sai định dạng.',
            'email.exists' => 'Email không tồn tại trên hệ thống.',
            'password.required' => 'Bạn phải nhập mật khẩu.',
            'password.confirmed' => 'Mật khẩu không khớp.',
            'password_confirmation.required' => 'Bạn chưa xác nhận mật khẩu.',
        ];
        $request->validate($rules, $messages);

        $token = DB::table('password_reset_tokens')
                            ->where([
                            'email' => $request->email,
                            'token' => $request->token
                            ])
                            ->first();
        if (!$token){
            return response([
                'error' => 'Token không hợp lệ!'
            ], 422);
        }

        User::where('email', $request->email)
                    ->update(['password' => bcrypt($request->password)]);

        //Delete the password reset request
        DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();
    }
}
