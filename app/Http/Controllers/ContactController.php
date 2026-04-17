<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactFormMail;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function show(): Response
    {
        $locale = App::getLocale();

        return Inertia::render('Contact', [
            'seo' => [
                'title' => $locale === 'pt' ? 'Fale Conosco' : 'Contact Us',
                'description' => $locale === 'pt'
                    ? 'Entre em contato com a Trataplan. Estamos prontos para atender você.'
                    : 'Get in touch with Trataplan. We are ready to assist you.',
                'canonical' => url()->current(),
            ],
        ]);
    }

    public function store(ContactFormRequest $request): RedirectResponse
    {
        $message = ContactMessage::create([
            ...$request->validated(),
            'locale' => App::getLocale(),
        ]);

        $contactEmail = Setting::get('contact_email', 'trataplan@trataplan.com.br');

        // Se o formulário enviou o e-mail da unidade selecionada, usa ele; caso contrário usa o padrão
        $toEmail = filter_var($request->input('unit_email'), FILTER_VALIDATE_EMAIL)
            ? $request->input('unit_email')
            : $contactEmail;

        // Aplica config SMTP do banco se configurada
        $mailHost = Setting::get('mail_host');
        if ($mailHost) {
            $port       = (int) Setting::get('mail_port', 587);
            // Porta 465 sempre usa SSL; normaliza "smtps"/"ssl" → "ssl", qualquer outra → "tls"
            $rawEnc     = strtolower(Setting::get('mail_encryption', 'tls'));
            $encryption = ($port === 465 || in_array($rawEnc, ['ssl', 'smtps'])) ? 'ssl' : 'tls';

            config([
                'mail.mailers.smtp.host'       => $mailHost,
                'mail.mailers.smtp.port'       => $port,
                'mail.mailers.smtp.username'   => Setting::get('mail_username'),
                'mail.mailers.smtp.password'   => Setting::get('mail_password'),
                'mail.mailers.smtp.encryption' => $encryption,
                'mail.mailers.smtp.scheme'     => null,
                'mail.from.address'            => Setting::get('mail_from_address', $contactEmail),
                'mail.from.name'               => Setting::get('mail_from_name', 'Trataplan'),
                'mail.default'                 => 'smtp',
            ]);
        }

        Mail::to($toEmail)->queue(new ContactFormMail($message));

        return back()->with('success', true);
    }
}
