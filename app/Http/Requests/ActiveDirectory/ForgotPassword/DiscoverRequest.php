<?php

namespace App\Http\Requests\ActiveDirectory\ForgotPassword;

use App\Http\Requests\Request;

class DiscoverRequest extends Request
{
    /**
     * The reset request validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|min:3',
        ];
    }

    /**
     * Allows all users to reset passwords.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
