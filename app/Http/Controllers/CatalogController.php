<?php

namespace App\Http\Controllers;

use App\Models\PortfolioCategory;
use App\Models\PortfolioService;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(): Response
    {
        $locale = App::getLocale();

        // Product categories (filters sidebar)
        $productCategories = ProductCategory::orderBy('order')->get(['id', 'name_pt', 'name_en'])->map(fn ($c) => [
            'id'   => $c->id,
            'name' => $locale === 'en' ? ($c->name_en ?: $c->name_pt) : $c->name_pt,
        ])->toArray();

        // Products grouped by category
        $products = PortfolioCategory::where('is_active', true)
            ->leftJoin('product_categories', 'portfolio_categories.product_category_id', '=', 'product_categories.id')
            ->orderBy('product_categories.order')
            ->orderBy('portfolio_categories.id')
            ->with(['photos', 'productCategory'])
            ->select('portfolio_categories.*')
            ->get()
            ->map(fn ($c) => [
                'id'                  => $c->id,
                'type'                => 'product',
                'name'                => $locale === 'en' ? ($c->name_en ?: $c->name_pt) : $c->name_pt,
                'description'         => $locale === 'en' ? ($c->description_en ?: $c->description_pt) : $c->description_pt,
                'cover_url'           => $c->cover_url ?? $c->photos->first()?->url,
                'product_category_id' => $c->product_category_id,
                'category_label'      => $c->productCategory
                                            ? ($locale === 'en' ? ($c->productCategory->name_en ?: $c->productCategory->name_pt) : $c->productCategory->name_pt)
                                            : ($locale === 'en' ? 'Products' : 'Produto'),
            ]);

        // Services (mão de obra)
        $services = PortfolioService::active()
            ->with('photos')
            ->get()
            ->map(fn ($s) => [
                'id'             => $s->id,
                'type'           => 'service',
                'name'           => $locale === 'en' ? ($s->name_en ?: $s->name_pt) : $s->name_pt,
                'description'    => $locale === 'en' ? ($s->description_en ?: $s->description_pt) : $s->description_pt,
                'cover_url'      => $s->cover_url ?? $s->photos->first()?->url,
                'product_category_id' => null,
                'category_label' => $locale === 'en' ? 'Specialized Services' : 'Mão de Obra',
            ]);

        $items = $products->concat($services)->values()->all();

        return Inertia::render('Catalog', [
            'items'             => $items,
            'productCategories' => $productCategories,
            'seo' => [
                'title'       => $locale === 'en' ? 'Catalog — Trataplan' : 'Catálogo — Trataplan',
                'description' => $locale === 'en'
                    ? 'Browse all Trataplan products and specialized services.'
                    : 'Conheça todos os produtos e serviços especializados da Trataplan.',
                'canonical'   => url()->current(),
            ],
        ]);
    }

    public function showProduct(PortfolioCategory $category): Response
    {
        $locale = App::getLocale();

        $photos = $category->photos->map(fn ($p) => $p->url)->values()->all();
        $coverUrl = $category->cover_url ?? ($photos[0] ?? null);

        return Inertia::render('CatalogItem', [
            'item' => [
                'id'             => $category->id,
                'type'           => 'product',
                'name'           => $locale === 'en' ? ($category->name_en ?: $category->name_pt) : $category->name_pt,
                'name_secondary' => $locale === 'en' ? $category->name_pt : $category->name_en,
                'description'    => $locale === 'en' ? ($category->description_en ?: $category->description_pt) : $category->description_pt,
                'cover_url'      => $coverUrl,
                'gallery_urls'   => $photos,
                'category_label' => $category->productCategory
                    ? ($locale === 'en' ? ($category->productCategory->name_en ?: $category->productCategory->name_pt) : $category->productCategory->name_pt)
                    : ($locale === 'en' ? 'Product' : 'Produto'),
                'back_url'       => $locale === 'en' ? '/en/catalog' : '/catalogo',
                'detail_url'     => url()->current(),
            ],
            'seo' => [
                'title'       => ($locale === 'en' ? ($category->name_en ?: $category->name_pt) : $category->name_pt) . ' — Trataplan',
                'description' => $locale === 'en'
                    ? ($category->description_en ?: $category->description_pt ?: '')
                    : ($category->description_pt ?: ''),
                'image'     => $coverUrl,
                'canonical' => url()->current(),
            ],
        ]);
    }

    public function showService(PortfolioService $service): Response
    {
        $locale = App::getLocale();

        $photos = $service->photos->map(fn ($p) => $p->url)->values()->all();
        $coverUrl = $service->cover_url ?? ($photos[0] ?? null);

        return Inertia::render('CatalogItem', [
            'item' => [
                'id'             => $service->id,
                'type'           => 'service',
                'name'           => $locale === 'en' ? ($service->name_en ?: $service->name_pt) : $service->name_pt,
                'name_secondary' => $locale === 'en' ? $service->name_pt : $service->name_en,
                'description'    => $locale === 'en' ? ($service->description_en ?: $service->description_pt) : $service->description_pt,
                'cover_url'      => $coverUrl,
                'gallery_urls'   => $photos,
                'category_label' => $locale === 'en' ? 'Specialized Services' : 'Mão de Obra Especializada',
                'back_url'       => $locale === 'en' ? '/en/catalog' : '/catalogo',
                'detail_url'     => url()->current(),
            ],
            'seo' => [
                'title'       => ($locale === 'en' ? ($service->name_en ?: $service->name_pt) : $service->name_pt) . ' — Trataplan',
                'description' => $locale === 'en'
                    ? ($service->description_en ?: $service->description_pt ?: '')
                    : ($service->description_pt ?: ''),
                'image'     => $coverUrl,
                'canonical' => url()->current(),
            ],
        ]);
    }
}
