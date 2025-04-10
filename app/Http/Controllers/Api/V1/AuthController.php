<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseV1Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\UserRole;
use App\Registration;
use App\Employee;
use App\AppMeta;

class AuthController extends BaseV1Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Login user and create token
     *
     * @param  \App\Http\Requests\Api\V1\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        // Check if user exists
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->unauthorizedResponse('Invalid credentials');
        }

        // Check if user is active
        if ($user->status != 1) {
            return $this->forbiddenResponse('User account is inactive');
        }

        // Get user role
        $role = UserRole::find($user->role_id);

        // Get user details based on role
        $userDetails = null;
        if ($role->name == 'Student') {
            $userDetails = Registration::with(['student', 'class', 'section', 'academic_year'])
                ->where('student_id', $user->id)
                ->where('is_promoted', 0)
                ->first();
        } elseif ($role->name == 'Teacher') {
            $userDetails = Employee::where('user_id', $user->id)->first();
        }

        // Create token
        $token = $user->createToken($request->username)->plainTextToken;

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $role->name,
                'details' => $userDetails
            ],
            'token' => $token
        ], 'Login successful');
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->successResponse(null, 'Logout successful');
    }

    /**
     * Get the authenticated User
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $role = UserRole::find($user->role_id);

        // Get user details based on role
        $userDetails = null;
        if ($role->name == 'Student') {
            $userDetails = Registration::with(['student', 'class', 'section', 'academic_year'])
                ->where('student_id', $user->id)
                ->where('is_promoted', 0)
                ->first();
        } elseif ($role->name == 'Teacher') {
            $userDetails = Employee::where('user_id', $user->id)->first();
        }

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $role->name,
                'details' => $userDetails
            ]
        ], 'User retrieved successfully');
    }

    /**
     * Change user password
     *
     * @param  \App\Http\Requests\Api\V1\ChangePasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse('Current password is incorrect', 400);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->successResponse(null, 'Password changed successfully');
    }

    /**
     * Get app information
     *
     * @return \Illuminate\Http\Response
     */
    public function appInfo()
    {
        $appMeta = AppMeta::first();

        return $this->successResponse([
            'app_name' => $appMeta->meta_value('institute_name'),
            'app_logo' => asset('storage/logo/' . $appMeta->meta_value('logo')),
            'app_version' => '1.0.0',
            'min_version' => '1.0.0',
            'force_update' => false,
            'contact_email' => $appMeta->meta_value('email'),
            'contact_phone' => $appMeta->meta_value('phone_no'),
            'website' => $appMeta->meta_value('website')
        ], 'App information retrieved successfully');
    }
}
