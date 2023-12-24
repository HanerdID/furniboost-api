<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Import Hash
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class ConfirmPasswordController extends Controller
{
    /**
     * Confirm the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password does not match your current password.'],
            ]);
        }

        return response()->json(['message' => 'Password confirmed.'], Response::HTTP_OK);
    }
}
