<?php

namespace App\Http\Requests\ActiveDirectory;

use App\Http\Requests\Request;

class SetupQuestionRequest extends Request
{
    /**
     * The security question setup request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'question' => 'required|integer',
            'answer'   => 'required',
        ];
    }

    /**
     * Allows all users to setup security questions.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
