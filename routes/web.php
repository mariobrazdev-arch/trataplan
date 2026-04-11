<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\AboutAdminController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PartnerAdminController;
use App\Http\Controllers\Admin\PortfolioCategoryAdminController;
use App\Http\Controllers\Admin\PortfolioServiceAdminController;
use App\Http\Controllers\Admin\PostAdminController;
use App\Http\Controllers\Admin\PostCategoryAdminController;
use App\Http\Controllers\Admin\ProductCategoryAdminController;
use App\Http\Controllers\Admin\ContactAdminController;
use App\Http\Controllers\Admin\SettingsAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes — Portuguese (default)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [AboutController::class, 'show'])->name('about');
Route::get('/catalogo', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalogo/produto/{category}', [CatalogController::class, 'showProduct'])->name('catalog.product');
Route::get('/catalogo/servico/{service}', [CatalogController::class, 'showService'])->name('catalog.service');
Route::get('/blog', [PostController::class, 'index'])->name('blog');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('blog.show');
Route::get('/parceiros', [PartnerController::class, 'index'])->name('partners');
Route::get('/contato', [ContactController::class, 'show'])->name('contact');
Route::post('/contato', [ContactController::class, 'store'])->name('contact.store');

/*
|--------------------------------------------------------------------------
| Public routes — English (/en prefix)
|--------------------------------------------------------------------------
*/
Route::prefix('en')->name('en.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/about', [AboutController::class, 'show'])->name('about');
    Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
    Route::get('/catalog/product/{category}', [CatalogController::class, 'showProduct'])->name('catalog.product');
    Route::get('/catalog/service/{service}', [CatalogController::class, 'showService'])->name('catalog.service');
    Route::get('/blog', [PostController::class, 'index'])->name('blog');
    Route::get('/blog/{slug}', [PostController::class, 'show'])->name('blog.show');
    Route::get('/partners', [PartnerController::class, 'index'])->name('partners');
    Route::get('/contact', [ContactController::class, 'show'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
});

/*
|--------------------------------------------------------------------------
| Sitemap & robots
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin routes — authenticated + admin role
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // About (Página Sobre)
    Route::get('/sobre', [AboutAdminController::class, 'index'])->name('about.index');
    Route::get('/sobre/criar', [AboutAdminController::class, 'create'])->name('about.create');
    Route::post('/sobre', [AboutAdminController::class, 'store'])->name('about.store');
    Route::post('/sobre/{about}/ativar', [AboutAdminController::class, 'activate'])->name('about.activate');
    Route::delete('/sobre/{about}', [AboutAdminController::class, 'destroy'])->name('about.destroy');

    // Posts
    Route::get('/posts', [PostAdminController::class, 'index'])->name('posts.index');
    Route::get('/posts/criar', [PostAdminController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostAdminController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/editar', [PostAdminController::class, 'edit'])->name('posts.edit');
    Route::patch('/posts/{post}', [PostAdminController::class, 'update'])->name('posts.update');
    Route::delete('/posts/fotos/{photo}', [PostAdminController::class, 'destroyPhoto'])->name('posts.destroyPhoto');
    Route::delete('/posts/{post}', [PostAdminController::class, 'destroy'])->name('posts.destroy');

    // Post Categories
    Route::get('/post-categorias', [PostCategoryAdminController::class, 'index'])->name('post-categories.index');
    Route::post('/post-categorias', [PostCategoryAdminController::class, 'store'])->name('post-categories.store');
    Route::patch('/post-categorias/{postCategory}', [PostCategoryAdminController::class, 'update'])->name('post-categories.update');
    Route::delete('/post-categorias/{postCategory}', [PostCategoryAdminController::class, 'destroy'])->name('post-categories.destroy');

    // Partners
    Route::get('/parceiros', [PartnerAdminController::class, 'index'])->name('partners.index');
    Route::get('/parceiros/criar', [PartnerAdminController::class, 'create'])->name('partners.create');
    Route::post('/parceiros', [PartnerAdminController::class, 'store'])->name('partners.store');
    Route::get('/parceiros/{partner}/editar', [PartnerAdminController::class, 'edit'])->name('partners.edit');
    Route::patch('/parceiros/{partner}', [PartnerAdminController::class, 'update'])->name('partners.update');
    Route::delete('/parceiros/{partner}', [PartnerAdminController::class, 'destroy'])->name('partners.destroy');
    Route::post('/parceiros/reorder', [PartnerAdminController::class, 'reorder'])->name('partners.reorder');

    // Settings
    Route::get('/configuracoes', [SettingsAdminController::class, 'index'])->name('settings');
    Route::patch('/configuracoes', [SettingsAdminController::class, 'update'])->name('settings.update');

    // Product Categories
    Route::get('/produto-categorias', [ProductCategoryAdminController::class, 'index'])->name('product-categories.index');
    Route::post('/produto-categorias', [ProductCategoryAdminController::class, 'store'])->name('product-categories.store');
    Route::patch('/produto-categorias/{productCategory}', [ProductCategoryAdminController::class, 'update'])->name('product-categories.update');
    Route::delete('/produto-categorias/{productCategory}', [ProductCategoryAdminController::class, 'destroy'])->name('product-categories.destroy');

    // Portfolio — categories
    Route::get('/portfolio/categorias', [PortfolioCategoryAdminController::class, 'index'])->name('portfolio.categories.index');
    Route::get('/portfolio/categorias/criar', [PortfolioCategoryAdminController::class, 'create'])->name('portfolio.categories.create');
    Route::post('/portfolio/categorias', [PortfolioCategoryAdminController::class, 'store'])->name('portfolio.categories.store');
    Route::get('/portfolio/categorias/{category}/editar', [PortfolioCategoryAdminController::class, 'edit'])->name('portfolio.categories.edit');
    Route::patch('/portfolio/categorias/{category}', [PortfolioCategoryAdminController::class, 'update'])->name('portfolio.categories.update');
    Route::delete('/portfolio/categorias/{category}', [PortfolioCategoryAdminController::class, 'destroy'])->name('portfolio.categories.destroy');
    Route::delete('/portfolio/categoria-fotos/{photo}', [PortfolioCategoryAdminController::class, 'destroyPhoto'])->name('portfolio.categories.destroyPhoto');

    // Portfolio — services
    Route::get('/portfolio/servicos', [PortfolioServiceAdminController::class, 'index'])->name('portfolio.services.index');
    Route::get('/portfolio/servicos/criar', [PortfolioServiceAdminController::class, 'create'])->name('portfolio.services.create');
    Route::post('/portfolio/servicos', [PortfolioServiceAdminController::class, 'store'])->name('portfolio.services.store');
    Route::get('/portfolio/servicos/{service}/editar', [PortfolioServiceAdminController::class, 'edit'])->name('portfolio.services.edit');
    Route::patch('/portfolio/servicos/{service}', [PortfolioServiceAdminController::class, 'update'])->name('portfolio.services.update');
    Route::delete('/portfolio/servicos/{service}', [PortfolioServiceAdminController::class, 'destroy'])->name('portfolio.services.destroy');
    Route::delete('/portfolio/servico-fotos/{photo}', [PortfolioServiceAdminController::class, 'destroyPhoto'])->name('portfolio.services.destroyPhoto');

    // Contact messages
    Route::get('/mensagens', [ContactAdminController::class, 'index'])->name('contact.index');
    Route::get('/mensagens/{message}', [ContactAdminController::class, 'show'])->name('contact.show');
    Route::delete('/mensagens/{message}', [ContactAdminController::class, 'destroy'])->name('contact.destroy');

    // Users
    Route::get('/usuarios', [UserAdminController::class, 'index'])->name('users.index');
    Route::get('/usuarios/criar', [UserAdminController::class, 'create'])->name('users.create');
    Route::post('/usuarios', [UserAdminController::class, 'store'])->name('users.store');
    Route::get('/usuarios/{user}/editar', [UserAdminController::class, 'edit'])->name('users.edit');
    Route::patch('/usuarios/{user}', [UserAdminController::class, 'update'])->name('users.update');
    Route::delete('/usuarios/{user}', [UserAdminController::class, 'destroy'])->name('users.destroy');

    // Profile
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('profile.update');
});
