<?php

namespace App\Http\Requests\Ligacao;

use App\Http\Requests\BaseRequest;

class LigacaoIndex extends BaseRequest
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
            'per_page' => 'nullable|integer',
            'sort_by' => 'nullable|string',
            'sort_type' => 'nullable|in:asc,desc',
            'filter' => 'nullable|string'
        ];
    }
}
