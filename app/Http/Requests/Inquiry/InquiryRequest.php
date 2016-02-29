<?php

namespace App\Http\Requests\Inquiry;

use App\Http\Requests\Request;
use App\Models\Category;

class InquiryRequest extends Request
{
    /**
     * The inquiry request validation rules.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title'         => 'required|min:5',
            'description'   => 'min:5',
            'manager'       => '',
        ];

        if ($this->route()->getName() !== 'inquiries.update') {
            // If the user isn't updating the their request,
            // we'll make the category field required.
            $rules['category'] = 'required|integer|exists:categories,id,belongs_to,inquiries';
        }

        $id = $this->request->get('category');

        $category = Category::find($id);

        if ($category instanceof Category && $category->manager === true) {
            $rules['manager'] = 'required|exists:users,id';
        }

        return $rules;
    }

    /**
     * Allow all users to create inquiries.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
