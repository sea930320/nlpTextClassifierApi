<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\BaseRequest;

class ProfileStore extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'name' => 'required|string',
            'sex' => 'required|in:male,female,other',
            'age' => 'required|integer',
        ];
    }
}
