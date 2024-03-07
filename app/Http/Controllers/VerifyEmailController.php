<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;


class VerifyEmailController extends Controller
{
    public function verify($id)
    {

        $user = User::findOrFail($id);

        $user->update(['email_verified_at' => now()]);

        return view('success');
    }
}
