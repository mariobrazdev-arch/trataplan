<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContactAdminController extends Controller
{
    public function index(): Response
    {
        $messages = ContactMessage::latest()
            ->paginate(20)
            ->through(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'phone' => $m->phone,
                'subject' => $m->subject,
                'locale' => $m->locale,
                'is_read' => $m->is_read,
                'created_at' => $m->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Admin/Contact/Index', ['messages' => $messages]);
    }

    public function show(ContactMessage $message): Response
    {
        $message->markAsRead();

        return Inertia::render('Admin/Contact/Show', [
            'message' => [
                'id' => $message->id,
                'name' => $message->name,
                'email' => $message->email,
                'phone' => $message->phone,
                'subject' => $message->subject,
                'message' => $message->message,
                'locale' => $message->locale,
                'is_read' => $message->is_read,
                'created_at' => $message->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();
        return redirect()->route('admin.contact.index')->with('success', 'Mensagem excluída.');
    }
}
