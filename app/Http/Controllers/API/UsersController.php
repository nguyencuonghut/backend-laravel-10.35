<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::orderByDesc('id')->get());
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
            'role_id' => 'required',
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
            'role_id.required' => 'Bạn cần chọn vai trò',
        ];
        $request->validate($rules, $messages);

        if ('Admin' == Auth::user()->role->name) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->role_id = $request->role_id;
            $user->status_id = $request->status_id ? $request->status_id : 1; //Default status as Open
            $user->save();
            return new UserResource($user);
        }

        return response([
            'error' => 'Bạn không có quyền thêm người dùng!'
        ], 403);
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
        $request->validate($rules, $messages);

        $user = User::findOrFail($id);
        if ('Admin' == Auth::user()->role->name) {
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
        if ('Admin' == Auth::user()->role->name) {
            $user->delete();
            return response(null, 204);
        }

        return  response()->json(["error" => "Bạn không có quyền xóa người dùng!"], 403);
    }

    /**
     * Display the specified resource.
     */
    public function getAuthUser()
    {
        $user_id = Auth::user()->id;
        return new UserResource(User::findOrFail($user_id));
    }

    /**
     * Change user's password
     */
    public function changePassword(Request $request)
    {
        $rules = array(
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required',
        );

        $messages = [
            'password.required' => 'Bạn phải nhập mật khẩu.',
            'password.min' => 'Mật khẩu dài tối thiểu 6 ký tự',
            'password.confirmed' => 'Mật khẩu không khớp',
            'password_confirmation.required' => 'Bạn cần xác nhận mật khẩu',
        ];
        $request->validate($rules, $messages);

        $user = User::findOrFail(Auth::user()->id);
        $user->password = bcrypt($request->password);
        $user->save();
        return new UserResource($user);
    }
}
