<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = array(
            'name'    => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required',
        );

        $messages = [
            'name.required' => 'Bạn phải nhập tên.',
            'email.required' => 'Bạn phải nhập địa chỉ email.',
            'email.email' => 'Email sai định dạng.',
            'email.unique' => 'Email đã tồn tại trên hệ thống.',
            'password.required' => 'Bạn phải nhập mật khẩu.',
            'password.min' => 'Mật khẩu dài tối thiểu 6 ký tự',
            'password.confirmed' => 'Mật khẩu không khớp',
            'password_confirmation.required' => 'Bạn cần xác nhận mật khẩu',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response([
                'error' => $validator->errors()
            ], 404);
        }

        $user = User::create($request->all());
        if (Auth::user()->isAdmin()) {
            return new UserResource($user);
        }

        return  response()->json(["error" => "Bạn không có quyền thêm người dùng!"], 403);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new UserResource(User::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rules = array(
            'name'    => 'required',
            'email'    => 'required|email|unique:users,email,'.$id,
        );

        $messages = [
            'name.required' => 'Bạn phải nhập tên.',
            'email.required' => 'Bạn phải nhập địa chỉ email.',
            'email.email' => 'Email sai định dạng.',
            'email.unique' => 'Email đã tồn tại trên hệ thống.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response([
                'error' => $validator->errors()
            ], 404);
        }

        $user = User::findOrFail($id);
        if (Auth::user()->isAdmin()) {
            $user->update($request->all());
            return new UserResource($user);
        }

        return  response()->json(["error" => "Bạn không có quyền sửa người dùng!"], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if (Auth::user()->isAdmin()) {
            $user->delete();
            return response(null, 204);
        }

        return  response()->json(["error" => "Bạn không có quyền xóa người dùng!"], 403);
    }
}
