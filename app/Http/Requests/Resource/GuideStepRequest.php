<?php

namespace App\Http\Requests\Resource;

use App\Http\Requests\Request;

class GuideStepRequest extends Request
{
    /**
     * The attachment request validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image'       => 'image',
            'title'       => 'required|min:5',
            'description' => 'min:5',
        ];
    }

    /**
     * Allows only logged in users to upload attachments.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user() ? true : false;
    }
}
