<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostCategoryAdminController extends Controller
{
    public function index(): Response
    {
        $categories = PostCategory::orderBy('order')->latest('id')->get(['id', 'name_pt', 'name_en', 'description_pt', 'description_en', 'order']);

        return Inertia::render('Admin/PostCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_pt'        => ['required', 'string', 'max:150'],
            'name_en'        => ['nullable', 'string', 'max:150'],
            'description_pt' => ['nullable', 'string', 'max:500'],
            'description_en' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['order'] = PostCategory::max('order') + 1;

        PostCategory::create($validated);

        return redirect()->route('admin.post-categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function update(Request $request, PostCategory $postCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name_pt'        => ['required', 'string', 'max:150'],
            'name_en'        => ['nullable', 'string', 'max:150'],
            'description_pt' => ['nullable', 'string', 'max:500'],
            'description_en' => ['nullable', 'string', 'max:500'],
        ]);

        $postCategory->update($validated);

        return redirect()->route('admin.post-categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(PostCategory $postCategory): RedirectResponse
    {
        $postCategory->delete();

        return redirect()->route('admin.post-categories.index')
            ->with('success', 'Categoria removida com sucesso!');
    }
}
