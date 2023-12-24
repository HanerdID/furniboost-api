<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show_profile()
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'user' => $user,
        ], Response::HTTP_OK);
    }

    public function edit_profile(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'user' => $user,
        ], Response::HTTP_OK);
    }
}
