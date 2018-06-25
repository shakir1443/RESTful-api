<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class PassportController extends Controller
{

    public $successStatus = 200;

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['id'] =  $user->id;
            $success['name'] =  $user->name;
            $success['email'] =  $user->email;
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['status'] =  200;
            $success['message'] = "Login Successful!";
            return response()->json($success, $this->successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
              'name' => 'required|string|max:255',
              'mobileno' => 'required|string|max:15|unique:users',
              'email' => 'required|string|email|max:255|unique:users',
              'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
      //  $success['token'] =  $user->createToken('MyApp')->accessToken;

        return response()->json(['message' => "Registration Successful"], 201);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        //$user = Auth::user();
        Auth::user()->token()->revoke();
        return response()->json([
            'message' => 'User logged out',
            'status' => 200,
        ]);
    }
}
