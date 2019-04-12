<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\BaseRequest;

class ProfileUpdate extends BaseRequest
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
            'profile_id' => 'exists:profiles,id',
            'name' => 'required|string',
            'sex' => 'required|in:male,female,other',
            'age' => 'required|integer',
        ];
    }

    /**
     * @return array
     */
    public function validationData()
    {
        $this->merge(
            [
                'profile_id' => $this->route('profile')
            ]
        );

        return parent::validationData();
    }
}
