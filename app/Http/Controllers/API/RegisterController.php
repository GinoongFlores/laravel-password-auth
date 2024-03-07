<?php

namespace App\Mail;

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Mail\VerificationMail;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class RegisterController extends Controller
{
    public function index(): JsonResponse
    {
        $user = User::all();
        return $this->sendResponse($user->toArray(), 'User retrieved successfully.');
    }

    public function getCurrentUser() {
        return Auth::user();
    }

    // register
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()) {
            return $this->sendResponse($validator->errors(), 'Validation Error.', false);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;

        Mail::to($request->email)->send(new VerificationMail($user));


        return $this->sendResponse($success, 'User register successfully.');
    }

    public function login (Request $request): JsonResponse
    {
        // error_log(print_r($request->all(), true)); // print request data
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if($user->email_verified_at === null){
                return $this->sendError('Email not verified', ['error' => 'Email not verified'], 401, false);

                $success['token'] = $user->createToken('MyApp')->accessToken;
                $success['name'] = $user->name;
                $token = $success['token'];
                $profile = $success['name'];

                return response()->json([
                    'message' => 'Welcome '. $user->name,
                    'status' => $profile,
                    'token' => $token
                ]);
            }

        }

        else {
            return $this->sendError('Unauthorized', ['error' => 'Unauthorized'], 401, false);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
