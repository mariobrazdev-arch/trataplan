<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'unit_email' => ['nullable', 'email', 'max:150'],
            'unit_name'  => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        if (app()->getLocale() === 'en') {
            return [
                'name.required'    => 'Name is required.',
                'email.required'   => 'Email is required.',
                'email.email'      => 'Please enter a valid email address.',
                'message.required' => 'Message is required.',
                'message.min'      => 'Message must be at least 10 characters.',
            ];
        }

        return [
            'name.required'    => 'Nome é obrigatório.',
            'email.required'   => 'E-mail é obrigatório.',
            'email.email'      => 'Por favor, informe um e-mail válido.',
            'message.required' => 'A mensagem é obrigatória.',
            'message.min'      => 'A mensagem deve ter pelo menos 10 caracteres.',
        ];
    }
}
