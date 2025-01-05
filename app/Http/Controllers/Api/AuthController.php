<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $data = $request->validate([

            'name'          =>  'required|max:255',
            'email'         =>  'required|email|unique:users',
            'password'      =>    'required'
        ]);
        $name = strip_tags($data['name']);
        $email = strip_tags($data['email']);
        $password = Hash::make($data['password']);

        $user = User::create([

            'name'      =>$name,
            'email'     =>$email,
            'password'  =>$password
        ]);

        $user->assignRole('user');

        return response()->json([
            'status'    =>true,
            'message'   =>'New Account',
            'data'      => $user->createToken($email)->plainTextToken
        ],200);
    }//End Method

    public function login(Request $request)
{
    if ($request->isMethod('post')) {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $email = strip_tags($data['email']);
        $password = strip_tags($data['password']);

        // Find the user by email
        $user = User::where('email', $email)->first();

        if ($user) {
            // Verify password
            if (Hash::check($password, $user->password)) {
                return response()->json([
                    'status'  => true,
                    'message' => 'Login successful',
                    'data'    => $user->createToken($email)->plainTextToken
                ], 200);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Password is incorrect',
                    'data'    => null
                ], 401); // Use 401 for unauthorized
            }
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'User not found',
                'data'    => null
            ], 404); // Use 404 for not found
        }
    }

    return response()->json([
        'status'  => false,
        'message' => 'Invalid request method',
        'data'    => null
    ], 405); // Use 405 for method not allowed
}
}
