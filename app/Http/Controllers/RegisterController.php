<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\SendWelcomeEmail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request to register a new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        info('Register Started'. '' . 'RegisterController');
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        info('Validation passed'. '' . 'RegisterController');
        $user = User::create(['email' => $request->email]);

        info('User created with email: ' . $user->email . ' ' . 'RegisterController');

        dispatch(new SendWelcomeEmail($user->email));

        return response()->json(['message' => 'User registered successfully.'], 201);
    }
}
