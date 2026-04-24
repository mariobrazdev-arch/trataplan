<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductCategoryAdminController extends Controller
{
    public function index(): Response
    {
        $categories = ProductCategory::orderBy('order')->get(['id', 'order', 'name_pt', 'name_en', 'description_pt', 'description_en']);

        return Inertia::render('Admin/ProductCategories/Index', [
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

        $validated['order'] = ProductCategory::max('order') + 1;

        ProductCategory::create($validated);

        return redirect()->route('admin.product-categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function update(Request $request, ProductCategory $productCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name_pt'        => ['required', 'string', 'max:150'],
            'name_en'        => ['nullable', 'string', 'max:150'],
            'description_pt' => ['nullable', 'string', 'max:500'],
            'description_en' => ['nullable', 'string', 'max:500'],
        ]);

        $productCategory->update($validated);

        return redirect()->route('admin.product-categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        $productCategory->delete();

        return redirect()->route('admin.product-categories.index')
            ->with('success', 'Categoria removida com sucesso!');
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->order as $item) {
            ProductCategory::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['ok' => true]);
    }
}
