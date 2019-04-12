<?php

namespace App\Http\Requests\Ligacao;

use App\Http\Requests\BaseRequest;

class LigacaoStore extends BaseRequest
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
            'texto' => 'required|string',
            'data' => 'required|string',
            'status' => 'required|integer',
            'feitas.avaliacao' => 'nullable|string'
        ];
    }
}
