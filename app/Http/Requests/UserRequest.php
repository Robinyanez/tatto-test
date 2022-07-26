<?php

namespace App\Http\Requests;

use Config;
use Carbon\Carbon;
use App\Models\ApiLog;
use App\Interfaces\HttpCodeInterface;
use App\Traits\Utilities\Api\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest implements HttpCodeInterface
{
    use ApiResponse;
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
        return [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name'     => 'Nombre',
            'email'    => 'Email',
            'password' => 'Password',
        ];
    }

    public function messages()
    {
        return [
            'name.required'        => 'El campo :attribute es obligatorio.',
            'email.required'       => 'El campo :attribute es obligatorio.',
            'email.email'          => 'El campo :attribute es incorrecto.',
            'email.unique'         => 'El campo :attribute ya se encuentra registrado.',
            'password.required'    => 'El campo :attribute es obligatorio.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $url = Config::get('values.userUrl');

        $errorArrayLabel = '';

        foreach ($validator->errors()->all() as $e) {

            $errorArrayLabel .= $e.' ';
        }

        $error = new ApiLog;
        $error->input_date = Carbon::now()->format('Y-m-d H:i:s');
        $error->input_json = json_encode($this->request->all());
        $error->response_date = Carbon::now()->format('Y-m-d H:i:s');
        $error->response_json = json_encode($this->errorResponse('Errores de validación: '.$errorArrayLabel, self::UNPROCESSABLE_ENTITY));
        $error->response_code = self::UNPROCESSABLE_ENTITY;
        $error->url = $url.'/api/opcion/tatto/create/user';
        $error->save();

        throw new HttpResponseException($this->errorResponse('Errores de validación: '.$errorArrayLabel, self::UNPROCESSABLE_ENTITY));
    }
}
