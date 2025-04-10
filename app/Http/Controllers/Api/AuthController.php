<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\UserRole;
use App\Registration;
use App\Employee;
use App\AppMeta;

/**
 * AuthController
 * 
 * This controller handles the authentication for the mobile app.
 */
class AuthController extends Controller
{
    /**
     * Login user and create token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user exists
        $user = User::where('username', $request->username)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check if user is active
        if ($user->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'User account is inactive'
            ], 403);
        }

        // Create token
        $token = $user->createToken($request->device_name)->plainTextToken;
        
        // Get user role
        $role = UserRole::find($user->role_id);
        
        // Get user details based on role
        $userDetails = null;
        if ($role->name == 'Student') {
            $userDetails = Registration::with('student', 'class', 'section')
                ->where('student_id', $user->id)
                ->first();
        } elseif ($role->name == 'Teacher' || $role->name == 'Employee') {
            $userDetails = Employee::find($user->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $role->name,
                    'details' => $userDetails
                ],
                'token' => $token
            ]
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Get the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        // Get user role
        $role = UserRole::find($user->role_id);
        
        // Get user details based on role
        $userDetails = null;
        if ($role->name == 'Student') {
            $userDetails = Registration::with('student', 'class', 'section')
                ->where('student_id', $user->id)
                ->first();
        } elseif ($role->name == 'Teacher' || $role->name == 'Employee') {
            $userDetails = Employee::find($user->id);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $role->name,
                    'details' => $userDetails
                ]
            ]
        ]);
    }

    /**
     * Change user password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Get app information
     *
     * @return \Illuminate\Http\Response
     */
    public function appInfo()
    {
        $appMeta = AppMeta::first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'app_name' => $appMeta->meta_value('institute_name'),
                'app_logo' => asset('storage/logo/' . $appMeta->meta_value('logo')),
                'app_version' => '1.0.0',
                'min_version' => '1.0.0',
                'force_update' => false,
                'contact_email' => $appMeta->meta_value('email'),
                'contact_phone' => $appMeta->meta_value('phone_no'),
                'website' => $appMeta->meta_value('website')
            ]
        ]);
    }
}
