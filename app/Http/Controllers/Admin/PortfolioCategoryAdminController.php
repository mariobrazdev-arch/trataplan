<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioCategory;
use App\Models\PortfolioCategoryPhoto;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioCategoryAdminController extends Controller
{
    private const MAX_PHOTOS = 25;

    private function productCategoriesList(): array
    {
        return ProductCategory::orderBy('name_pt')->get(['id', 'name_pt'])
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name_pt])
            ->toArray();
    }

    public function index(): Response
    {
        $categories = PortfolioCategory::with(['photos', 'productCategory'])->orderBy('name_pt')->get()->map(fn ($c) => [
            'id'        => $c->id,
            'name_pt'   => $c->name_pt,
            'description_pt' => $c->description_pt,
            'cover_url' => $c->cover_url ?? $c->photos->first()?->url,
            'is_active' => $c->is_active,
            'photos_count' => $c->photos->count(),
            'product_category_id'   => $c->product_category_id,
            'product_category_name' => $c->productCategory?->name_pt,
        ]);

        return Inertia::render('Admin/Portfolio/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Portfolio/Categories/Form', [
            'category'          => null,
            'productCategories' => $this->productCategoriesList(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'name_pt'   => ['required', 'string', 'max:150'],
            'description_pt' => ['nullable', 'string'],
            'cover_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_active' => ['boolean'],
            'new_photos' => ['nullable', 'array', 'max:' . self::MAX_PHOTOS],
            'new_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'new_photos.max' => 'Cada item pode ter no máximo ' . self::MAX_PHOTOS . ' fotos.',
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
                'id'                  => $category->id,
                'product_category_id' => $category->product_category_id,
                'name_pt'             => $category->name_pt,
                'description_pt'      => $category->description_pt,
                'cover_url'           => $category->cover_url,
                'is_active'           => $category->is_active,
                'photos' => $category->photos->map(fn ($ph) => [
                    'id' => $ph->id,
                    'url' => $ph->url,
                ]),
            ],
            'productCategories' => $this->productCategoriesList(),
        ]);
    }

    public function update(Request $request, PortfolioCategory $category): RedirectResponse
    {
        $existingPhotosCount = $category->photos()->count();

        $validated = $request->validate([
            'product_category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'name_pt'   => ['required', 'string', 'max:150'],
            'description_pt' => ['nullable', 'string'],
            'cover_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_active' => ['boolean'],
            'new_photos' => ['nullable', 'array', 'max:' . max(0, self::MAX_PHOTOS - $existingPhotosCount)],
            'new_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'new_photos.max' => 'Cada item pode ter no máximo ' . self::MAX_PHOTOS . ' fotos. Este item já possui ' . $existingPhotosCount . '.',
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
