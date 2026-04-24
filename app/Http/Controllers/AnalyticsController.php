<?php

namespace App\Http\Controllers;

use App\Models\PageView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnalyticsController extends Controller
{
    public function track(Request $request): JsonResponse
    {
        $url = $request->input('url', '/');

        // Ignora acessos ao admin
        $path = parse_url($url, PHP_URL_PATH) ?? '/';
        if (str_starts_with($path, '/admin')) {
            return response()->json(['ok' => true]);
        }

        $ip = $request->ip();
        [$country, $city] = $this->geolocate($ip);

        $ua = $request->userAgent() ?? '';

        $view = PageView::create([
            'url'      => substr($path, 0, 500),
            'title'    => substr($request->input('title', ''), 0, 200),
            'ip'       => $ip,
            'country'  => $country,
            'city'     => $city,
            'device'   => $this->detectDevice($ua),
            'browser'  => $this->detectBrowser($ua),
            'referrer' => substr($request->input('referrer', ''), 0, 500),
        ]);

        return response()->json(['ok' => true, 'id' => $view->id]);
    }

    public function duration(Request $request): JsonResponse
    {
        $id       = (int) $request->input('id');
        $duration = min((int) $request->input('duration', 0), 3600);

        if ($id > 0 && $duration > 0) {
            PageView::where('id', $id)->whereNull('duration')->update(['duration' => $duration]);
        }

        return response()->json(['ok' => true]);
    }

    private function geolocate(string $ip): array
    {
        // Pula IPs privados/locais
        if (in_array($ip, ['127.0.0.1', '::1']) || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return [null, null];
        }

        try {
            $res = Http::timeout(2)->get("http://ip-api.com/json/{$ip}?fields=status,country,city");
            $data = $res->json();
            if (($data['status'] ?? '') === 'success') {
                return [$data['country'] ?? null, $data['city'] ?? null];
            }
        } catch (\Throwable) {}

        return [null, null];
    }

    private function detectDevice(string $ua): string
    {
        if (preg_match('/iPad|Tablet/i', $ua))              return 'tablet';
        if (preg_match('/Mobile|Android|iPhone|iPod/i', $ua)) return 'mobile';
        return 'desktop';
    }

    private function detectBrowser(string $ua): string
    {
        if (str_contains($ua, 'Edg'))                              return 'Edge';
        if (str_contains($ua, 'OPR') || str_contains($ua, 'Opera')) return 'Opera';
        if (str_contains($ua, 'Chrome'))                           return 'Chrome';
        if (str_contains($ua, 'Firefox'))                          return 'Firefox';
        if (str_contains($ua, 'Safari'))                           return 'Safari';
        return 'Outro';
    }
}
