<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioCategory;
use App\Models\PortfolioCategoryPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioCategoryAdminController extends Controller
{
    public function index(): Response
    {
        $categories = PortfolioCategory::with('photos')->latest('id')->get()->map(fn ($c) => [
            'id'        => $c->id,
            'name_pt'   => $c->name_pt,
            'name_en'   => $c->name_en,
            'description_pt' => $c->description_pt,
            'description_en' => $c->description_en,
            'cover_url' => $c->cover_url ?? $c->photos->first()?->url,
            'is_active' => $c->is_active,
            'photos_count' => $c->photos->count(),
        ]);

        return Inertia::render('Admin/Portfolio/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Portfolio/Categories/Form', ['category' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_pt'   => ['required', 'string', 'max:150'],
            'name_en'   => ['required', 'string', 'max:150'],
            'description_pt' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'cover_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_active' => ['boolean'],
            'new_photos' => ['nullable', 'array'],
            'new_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $category = PortfolioCategory::create(collect($validated)->except(['cover_photo', 'new_photos'])->all());

        if ($request->hasFile('cover_photo')) {
            $coverPath = $request->file('cover_photo')->store("portfolio/categories/{$category->id}", 'public');
            $category->update(['cover_photo' => $coverPath]);
        }

        $firstUploadedPhotoPath = null;
        if ($request->hasFile('new_photos')) {
            foreach ($request->file('new_photos') as $i => $file) {
                $path = $file->store("portfolio/categories/{$category->id}", 'public');
                if ($i === 0) {
                    $firstUploadedPhotoPath = $path;
                }
                PortfolioCategoryPhoto::create([
                    'category_id' => $category->id,
                    'path' => $path,
                    'order' => $i,
                ]);
            }
        }

        if (!$category->cover_photo && $firstUploadedPhotoPath) {
            $category->update(['cover_photo' => $firstUploadedPhotoPath]);
        }

        return redirect()->route('admin.portfolio.categories.index')
            ->with('success', 'Produto criado!');
    }

    public function edit(PortfolioCategory $category): Response
    {
        return Inertia::render('Admin/Portfolio/Categories/Form', [
            'category' => [
                'id'        => $category->id,
                'name_pt'   => $category->name_pt,
                'name_en'   => $category->name_en,
                'description_pt' => $category->description_pt,
                'description_en' => $category->description_en,
                'cover_url' => $category->cover_url,
                'is_active' => $category->is_active,
                'photos' => $category->photos->map(fn ($ph) => [
                    'id' => $ph->id,
                    'url' => $ph->url,
                ]),
            ],
        ]);
    }

    public function update(Request $request, PortfolioCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name_pt'   => ['required', 'string', 'max:150'],
            'name_en'   => ['required', 'string', 'max:150'],
            'description_pt' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'cover_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_active' => ['boolean'],
            'new_photos' => ['nullable', 'array'],
            'new_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $category->update(collect($validated)->except(['cover_photo', 'new_photos'])->all());

        if ($request->hasFile('cover_photo')) {
            if ($category->cover_photo) {
                Storage::disk('public')->delete($category->cover_photo);
            }
            $coverPath = $request->file('cover_photo')->store("portfolio/categories/{$category->id}", 'public');
            $category->update(['cover_photo' => $coverPath]);
        }

        $firstUploadedPhotoPath = null;
        if ($request->hasFile('new_photos')) {
            $nextOrder = $category->photos()->max('order') + 1;
            foreach ($request->file('new_photos') as $i => $file) {
                $path = $file->store("portfolio/categories/{$category->id}", 'public');
                if ($i === 0) {
                    $firstUploadedPhotoPath = $path;
                }
                PortfolioCategoryPhoto::create([
                    'category_id' => $category->id,
                    'path' => $path,
                    'order' => $nextOrder + $i,
                ]);
            }
        }

        if (!$category->cover_photo && $firstUploadedPhotoPath) {
            $category->update(['cover_photo' => $firstUploadedPhotoPath]);
        }

        return back()->with('success', 'Produto atualizado!');
    }

    public function destroyPhoto(PortfolioCategoryPhoto $photo): RedirectResponse
    {
        $categoryId = $photo->category_id;
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return redirect()->route('admin.portfolio.categories.edit', $categoryId)
            ->with('success', 'Foto removida.');
    }

    public function destroy(PortfolioCategory $category): RedirectResponse
    {
        if ($category->cover_photo) {
            Storage::disk('public')->delete($category->cover_photo);
        }

        foreach ($category->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $category->delete();

        return redirect()->route('admin.portfolio.categories.index')
            ->with('success', 'Produto removido.');
    }
}
