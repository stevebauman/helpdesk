<?php

namespace App\Http\Requests\ActiveDirectory\ForgotPassword;

use App\Http\Requests\Request;

class PasswordRequest extends Request
{
    /**
     * The password request validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password'              => 'required|confirmed|min:8',
            'password_confirmation' => 'required|min:8',
        ];
    }

    /**
     * Allows all users to change their passwords.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
