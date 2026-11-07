<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'min:3', 'max:100',
                'regex:/[a-zA-ZÀ-ÖØ-öø-ÿ]/',           // precisa conter ao menos uma letra
                'regex:/^[a-zA-ZÀ-ÖØ-öø-ÿ\s\'\-\.]+$/', // só letras, espaços e pontuação de nome
            ],
            'email' => ['required', 'email:rfc,dns', 'max:150'],
            'phone' => [
                'nullable', 'string', 'max:20',
                'regex:/^[\d\s\(\)\+\-]+$/',  // só dígitos e caracteres de formatação de telefone
                function ($attribute, $value, $fail) {
                    $digits = preg_replace('/\D/', '', (string) $value);
                    if ($digits === '') {
                        return;
                    }
                    if (strlen($digits) < 10) {
                        $fail($this->translateError('phone.min_digits'));
                        return;
                    }
                    // analisa só o núcleo do número (últimos 8 dígitos, ignorando DDD/país)
                    // para não confundir com repetições legítimas (ex: o "9999" de celulares BR)
                    $core = substr($digits, -8);

                    // núcleo inteiro com o mesmo dígito repetido (ex: 11111111)
                    if (preg_match('/^(\d)\1+$/', $core)) {
                        $fail($this->translateError('phone.sequence'));
                        return;
                    }
                    // núcleo inteiro é uma sequência crescente ou decrescente (ex: 34567890)
                    $isAscending  = true;
                    $isDescending = true;
                    for ($i = 1; $i < strlen($core); $i++) {
                        if ((int) $core[$i] !== ((int) $core[$i - 1] + 1) % 10) {
                            $isAscending = false;
                        }
                        if ((int) $core[$i] !== ((int) $core[$i - 1] - 1 + 10) % 10) {
                            $isDescending = false;
                        }
                    }
                    if ($isAscending || $isDescending) {
                        $fail($this->translateError('phone.sequence'));
                    }
                },
            ],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'unit_email' => ['nullable', 'email', 'max:150'],
            'unit_name'  => ['nullable', 'string', 'max:100'],
        ];
    }

    private function translateError(string $key): string
    {
        $isEn = app()->getLocale() === 'en';

        return match ($key) {
            'phone.min_digits' => $isEn ? 'Phone must have at least 10 digits.' : 'O telefone deve ter pelo menos 10 dígitos.',
            'phone.sequence'   => $isEn ? 'Please enter a valid phone number.' : 'Informe um telefone válido.',
            default => $isEn ? 'Invalid value.' : 'Valor inválido.',
        };
    }

    public function messages(): array
    {
        if (app()->getLocale() === 'en') {
            return [
                'name.required'    => 'Name is required.',
                'name.min'         => 'Name must have at least 3 characters.',
                'name.regex'       => 'Please enter a valid name (letters only).',
                'email.required'   => 'Email is required.',
                'email.email'      => 'Please enter a valid email address.',
                'phone.regex'      => 'Please enter a valid phone number.',
                'phone.not_regex'  => 'Please enter a valid phone number.',
                'message.required' => 'Message is required.',
                'message.min'      => 'Message must be at least 10 characters.',
            ];
        }

        return [
            'name.required'    => 'Nome é obrigatório.',
            'name.min'         => 'O nome deve ter pelo menos 3 caracteres.',
            'name.regex'       => 'Informe um nome válido (apenas letras).',
            'email.required'   => 'E-mail é obrigatório.',
            'email.email'      => 'Por favor, informe um e-mail válido.',
            'phone.regex'      => 'Informe um telefone válido.',
            'phone.not_regex'  => 'Informe um telefone válido.',
            'message.required' => 'A mensagem é obrigatória.',
            'message.min'      => 'A mensagem deve ter pelo menos 10 caracteres.',
        ];
    }
}
