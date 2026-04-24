<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsAdminController extends Controller
{
    public function index(Request $request): Response
    {
        $period = $request->query('period', '30d');

        $from = match ($period) {
            'today' => now()->startOfDay(),
            '7d'    => now()->subDays(7)->startOfDay(),
            'all'   => null,
            default => now()->subDays(30)->startOfDay(), // '30d'
        };

        $query = fn () => $from
            ? PageView::where('created_at', '>=', $from)
            : PageView::query();

        // ── Stat cards ──────────────────────────────────────────────────────
        $totalViews    = $query()->count();
        $uniqueVisitors = $query()->distinct('ip')->count('ip');
        $avgDuration   = (int) round($query()->whereNotNull('duration')->avg('duration') ?? 0);
        $mobilePct     = $totalViews > 0
            ? round($query()->where('device', 'mobile')->count() / $totalViews * 100)
            : 0;

        // ── Daily chart (last 30 days always) ───────────────────────────────
        $dailyRaw = PageView::where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as views'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('views', 'date')
            ->toArray();

        $daily = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $daily[] = ['date' => now()->subDays($i)->format('d/m'), 'views' => $dailyRaw[$d] ?? 0];
        }

        // ── Top pages ────────────────────────────────────────────────────────
        $topPages = $query()
            ->select('url', DB::raw('COUNT(*) as views'))
            ->groupBy('url')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        // ── Top países ───────────────────────────────────────────────────────
        $topCountries = $query()
            ->whereNotNull('country')
            ->select('country', DB::raw('COUNT(*) as views'))
            ->groupBy('country')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        // ── Top cidades ──────────────────────────────────────────────────────
        $topCities = $query()
            ->whereNotNull('city')
            ->select('city', 'country', DB::raw('COUNT(*) as views'))
            ->groupBy('city', 'country')
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        // ── Dispositivos ─────────────────────────────────────────────────────
        $devices = $query()
            ->select('device', DB::raw('COUNT(*) as views'))
            ->groupBy('device')
            ->orderByDesc('views')
            ->get()
            ->map(fn ($r) => ['device' => $r->device ?? 'Desconhecido', 'views' => $r->views]);

        // ── Navegadores ──────────────────────────────────────────────────────
        $browsers = $query()
            ->select('browser', DB::raw('COUNT(*) as views'))
            ->groupBy('browser')
            ->orderByDesc('views')
            ->limit(6)
            ->get()
            ->map(fn ($r) => ['browser' => $r->browser ?? 'Outro', 'views' => $r->views]);

        return Inertia::render('Admin/Analytics/Index', [
            'period'         => $period,
            'stats'          => [
                'total_views'     => $totalViews,
                'unique_visitors' => $uniqueVisitors,
                'avg_duration'    => $avgDuration,
                'mobile_pct'      => $mobilePct,
            ],
            'daily'          => $daily,
            'top_pages'      => $topPages,
            'top_countries'  => $topCountries,
            'top_cities'     => $topCities,
            'devices'        => $devices,
            'browsers'       => $browsers,
        ]);
    }
}
