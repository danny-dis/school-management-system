<?php

namespace App\Http\Requests\Api;

class StoreStudentRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone_no' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'required|integer|in:1,2',
            'religion' => 'nullable|integer',
            'blood_group' => 'nullable|integer',
            'nationality' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:2048',
            'father_name' => 'nullable|string|max:255',
            'father_phone_no' => 'nullable|string|max:20',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone_no' => 'nullable|string|max:20',
            'present_address' => 'nullable|string|max:500',
            'permanent_address' => 'nullable|string|max:500',
            'status' => 'nullable|integer|in:0,1',
            'username' => 'nullable|string|max:50|unique:users,username',
            'password' => 'nullable|string|min:6',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already in use.',
            'gender.required' => 'The gender field is required.',
            'gender.in' => 'The selected gender is invalid.',
            'username.unique' => 'This username is already in use.',
            'password.min' => 'The password must be at least 6 characters.',
        ];
    }
}
