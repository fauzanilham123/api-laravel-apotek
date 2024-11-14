<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\SendError;
use App\Http\Controllers\Controller;
use App\Http\Resources\SendResponse;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function Register(Request $request): SendError|SendResponse
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'username'     => 'required|unique:users',
            'telepon'     => 'required',
            'role'     => 'required',
            'alamat'     => 'required',
            'password'  => 'required|min:8|confirmed'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return new SendError(422, 'error', $validator->errors()); // You can customize this based on your SendError class
        }

        //create user
        $user = User::create([
            'name'      => $request->name,
            'username'     => $request->username,
            'telepon'     => $request->telepon,
            'role'     => $request->role,
            'alamat'     => $request->alamat,
            'flag'     => 1,
            'password'  => bcrypt($request->password)
        ]);

        return new SendResponse('success', $user);
    }

    public function Login(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'username'     => 'required',
            'password'  => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return new SendError(422, 'error', $validator->errors());
        }

        //get credentials from request
        $credentials = $request->only('username', 'password');

        //if auth failed
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return new SendError(401, 'error', "Incorrect username or password");
        }

        // If auth success
        $user = auth()->guard('api')->user();
        $data = [
            'token' => $token,
            'user' => $user
        ];

        //if auth success
        return new SendResponse('success', $data);
    }
}