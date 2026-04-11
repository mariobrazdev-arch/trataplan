<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Partner;
use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'posts_count' => Post::count(),
                'published_posts' => Post::published()->count(),
                'partners_count' => Partner::where('is_active', true)->count(),
                'unread_messages' => ContactMessage::unread()->count(),
            ],
            'recent_messages' => ContactMessage::latest()->take(5)->get()->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'subject' => $m->subject,
                'locale' => $m->locale,
                'is_read' => $m->is_read,
                'created_at' => $m->created_at->format('d/m/Y H:i'),
            ]),
        ]);
    }
}
