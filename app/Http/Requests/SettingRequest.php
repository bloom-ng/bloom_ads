<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->isAdmin();
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'value' => 'required|string',
        ];

        // Only validate key uniqueness on creation
        if ($this->isMethod('post')) {
            $rules['key'] = 'required|string|max:255|unique:settings,key';
        }

        return $rules;
    }
} 