<?php

namespace App\Http\Requests\ActiveDirectory\ForgotPassword;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

class QuestionRequest extends Request
{
    /**
     * The question request validation rules.
     *
     * @return array
     */
    public function rules()
    {
        $questions = $this->request->get('questions');

        if (is_array($questions)) {
            $rules = [];

            foreach ($questions as $key => $question) {
                // We need to go through each question and create
                // a dot-notated rule for laravel's validation.
                $rules['questions.'.$key] = 'required';
            }

            return $rules;
        }

        // Default required rule for redirect.
        return [
            'questions' => 'required',
        ];
    }

    /**
     * Formats the question request validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => 'This question is required.',
        ];
    }

    /**
     * Formats the validators errors to allow any number of security questions.
     *
     * @param Validator $validator
     *
     * @return array
     */
    protected function formatErrors(Validator $validator)
    {
        $errors = $validator->getMessageBag()->toArray();

        $processed = [];

        foreach ($errors as $key => $error) {
            $parts = explode('.', $key);

            // If we have exactly two parts, we can work with the error message.
            if (count($parts) === 2) {
                $name = $parts[0];
                $key = $parts[1];

                $field = sprintf('%s[%s]', $name, $key);

                $processed[$field] = $error;
            }
        }

        return $processed;
    }

    /**
     * Allows all users to answer their security questions.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
