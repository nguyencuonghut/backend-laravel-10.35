<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
}
