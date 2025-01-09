<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required_if:id,null|email|unique:users,email',
            'name' => 'required_if:id,null|min:3|unique:users,name',
            'role_id' => 'nullable',
            'password' => 'required_if:id,null|min:8|max:255',
            // 'password_confirmation' => 'required_if:id,null',
            // 'password' => [
            //     'required',
            //     'confirmed',
            //     Password::min(8)
            //         ->letters()
            //         ->mixedCase()
            //         ->numbers()
            //         ->symbols()
            //         ->uncompromised(),
            // ],


        ];
    }

    public function messages()
    {
        return [
            // Email validation messages
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',

            // Name validation messages
            'name.required' => 'The name field is required.',
            'name.min' => 'The name must be at least 3 characters long.',
            'name.unique' => 'This name is already registered.',

            // Password validation messages
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.max' => 'The password may not exceed 255 characters.',
        ];
    }
}
