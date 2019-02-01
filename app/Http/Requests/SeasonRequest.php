<?php

namespace HappyFeet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeasonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|unique:season,name',
            'start_date' => 'required',
            'end_date' => 'required',
            'inscription_price' => 'required',
            'month_price' => 'required'
        ];


        if ($this->method() == 'PUT') {
            $rules['name'] = "required|unique:season,name,".$this->get('key');
        }


        return $rules;


    }

    public function messages()
    {
        return [
            'name.required' => 'Por favor ingrese un nombre',
            'name.unique' => 'Por favor ingrese otro nombre',
            'start_date.required' => 'Por favor, Ingrese una duración',
            'end_date.required' => 'Por favor, Ingrese una duración',
            'inscription_price.required' => 'Por favor, ingrese  un precio',
            'month_price.required' => 'Por favor, ingrese un precio',
        ];
    }
}