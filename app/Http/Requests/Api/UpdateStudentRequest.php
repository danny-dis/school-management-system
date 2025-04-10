<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class UpdateStudentRequest extends ApiRequest
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
        $student = $this->route('student');
        
        return [
            'name' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($student->user_id)
            ],
            'phone_no' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'nullable|integer|in:1,2',
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
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already in use.',
            'gender.in' => 'The selected gender is invalid.',
            'password.min' => 'The password must be at least 6 characters.',
        ];
    }
}
