<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PageAdminController extends Controller
{
    public function index(): Response
    {
        $pages = Page::orderBy('order')->get()->map(fn ($page) => [
            'id' => $page->id,
            'slug' => $page->slug,
            'title_pt' => $page->title_pt,
            'title_en' => $page->title_en,
            'is_published' => $page->is_published,
            'order' => $page->order,
            'updated_at' => $page->updated_at->format('d/m/Y'),
        ]);

        return Inertia::render('Admin/Pages/Index', ['pages' => $pages]);
    }

    public function edit(Page $page): Response
    {
        return Inertia::render('Admin/Pages/Form', ['page' => $page]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title_pt' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'content_pt' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'meta_title_pt' => ['nullable', 'string', 'max:60'],
            'meta_title_en' => ['nullable', 'string', 'max:60'],
            'meta_description_pt' => ['nullable', 'string', 'max:160'],
            'meta_description_en' => ['nullable', 'string', 'max:160'],
            'is_published' => ['boolean'],
        ]);

        $page->update($validated);

        return back()->with('success', 'Página atualizada!');
    }
}
