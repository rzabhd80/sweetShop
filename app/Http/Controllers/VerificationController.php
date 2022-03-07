<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{

    public function resend()
    {
        if (request()->user()->hasVerifiedEmail())
            return response()->json(["your email is already verified"], 403);
        else request()->user()->sendEmailVerification();
        return response()->json(["message" => "email verification link has been sent to your email"]);
    }


    public function verify()
    {
        if (!request()->hasValidSignature())
            return response()->json(["message" => "link has expired"], 403);
        if (request()->user()->hasVerifiedEmail())
            request()->user()->markEmailAsVerified();
    }
}
