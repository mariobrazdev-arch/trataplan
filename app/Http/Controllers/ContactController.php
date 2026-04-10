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

        $contactEmail = Setting::get('contact_email', config('mail.from.address'));

        Mail::to($contactEmail)->queue(new ContactFormMail($message));

        return back()->with('success', true);
    }
}
