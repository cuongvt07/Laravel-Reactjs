<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class LoginRequest extends FormRequest
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
        $action = URL::current();
        switch ($action):
            case 'login':
                $this->rules = [
                    'email' => ['required','string'],
                    'password' => ['required']
                ];
                break;
            case 'register':
                $this->rules = [
                    'email' => ['required','string'],
                    'name' => ['required','string'],
                    'password' => ['required']
                ];
                break;
            case 'reply':
            case 'delete':
            case 'create':
        endswitch;
        return $this->messages();
    }

    public function messages()
    {
        return[
            'email.required' => 'Email bắt buộc',
        ];
    }

}
