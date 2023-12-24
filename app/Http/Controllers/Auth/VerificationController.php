<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        if ($request->route('id') != $request->user()->getKey()) {
            return response()->json(['message' => 'Invalid verification URL'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], Response::HTTP_OK);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json(['message' => 'Email successfully verified'], Response::HTTP_OK);
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], Response::HTTP_OK);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Email verification link resent'], Response::HTTP_OK);
    }
}
