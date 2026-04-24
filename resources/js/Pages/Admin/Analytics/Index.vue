<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: null });

const props = defineProps({
    period:        String,
    stats:         Object,
    daily:         Array,
    top_pages:     Array,
    top_countries: Array,
    top_cities:    Array,
    devices:       Array,
    browsers:      Array,
});

const periods = [
    { key: 'today', label: 'Hoje' },
    { key: '7d',    label: '7 dias' },
    { key: '30d',   label: '30 dias' },
    { key: 'all',   label: 'Tudo' },
];

function setPeriod(p) {
    router.get('/admin/analytics', { period: p }, { preserveState: true, replace: true });
}

function formatDuration(secs) {
    if (!secs) return '—';
    if (secs < 60) return `${secs}s`;
    return `${Math.floor(secs / 60)}m ${secs % 60}s`;
}

function cleanUrl(url) {
    return url === '/' ? 'Início' : url;
}

const maxDaily = computed(() => Math.max(...(props.daily ?? []).map(d => d.views), 1));

const deviceIcons = { mobile: 'smartphone', tablet: 'tablet', desktop: 'computer' };
const deviceColors = { mobile: '#fbbf24', tablet: '#34d399', desktop: '#60a5fa' };
const browserIcons = { Chrome: 'language', Firefox: 'travel_explore', Safari: 'language', Edge: 'language', Opera: 'language', Outro: 'public' };

const totalDevices = computed(() => (props.devices ?? []).reduce((s, d) => s + d.views, 0));
</script>

<template>
    <AdminLayout>
        <template #title>Analytics</template>
        <template #subtitle>Estatísticas de acesso ao site público (admin excluído)</template>

        <!-- ── Seletor de período ── -->
        <div class="flex gap-1 rounded-xl p-1 mb-8 w-fit" style="background-color: #f0ede8;">
            <button
                v-for="p in periods" :key="p.key"
                type="button"
                @click="setPeriod(p.key)"
                class="px-5 py-2 rounded-lg text-sm font-bold transition-all"
                :style="period === p.key
                    ? 'background-color: #012d1d; color: #fff;'
                    : 'color: #717973;'"
            >
                {{ p.label }}
            </button>
        </div>

        <!-- ── Stat cards ── -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="rounded-2xl p-6 bg-white shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #e8f5e9;">
                        <span class="material-symbols-outlined text-xl" style="color: #012d1d;">visibility</span>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest" style="color: #9ca3af; font-family: Manrope, sans-serif;">Visitas</span>
                </div>
                <p class="text-3xl font-black" style="color: #012d1d; font-family: Manrope, sans-serif;">{{ stats.total_views.toLocaleString('pt-BR') }}</p>
            </div>

            <div class="rounded-2xl p-6 bg-white shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #fef3c7;">
                        <span class="material-symbols-outlined text-xl" style="color: #d97706;">person</span>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest" style="color: #9ca3af; font-family: Manrope, sans-serif;">Visitantes únicos</span>
                </div>
                <p class="text-3xl font-black" style="color: #012d1d; font-family: Manrope, sans-serif;">{{ stats.unique_visitors.toLocaleString('pt-BR') }}</p>
            </div>

            <div class="rounded-2xl p-6 bg-white shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #ede9fe;">
                        <span class="material-symbols-outlined text-xl" style="color: #7c3aed;">timer</span>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest" style="color: #9ca3af; font-family: Manrope, sans-serif;">Tempo médio</span>
                </div>
                <p class="text-3xl font-black" style="color: #012d1d; font-family: Manrope, sans-serif;">{{ formatDuration(stats.avg_duration) }}</p>
            </div>

            <div class="rounded-2xl p-6 bg-white shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #fee2e2;">
                        <span class="material-symbols-outlined text-xl" style="color: #dc2626;">smartphone</span>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest" style="color: #9ca3af; font-family: Manrope, sans-serif;">% Mobile</span>
                </div>
                <p class="text-3xl font-black" style="color: #012d1d; font-family: Manrope, sans-serif;">{{ stats.mobile_pct }}%</p>
            </div>
        </div>

        <!-- ── Gráfico diário ── -->
        <div class="rounded-2xl p-6 bg-white shadow-sm mb-8">
            <h3 class="text-sm font-black uppercase tracking-widest mb-6" style="color: #717973; font-family: Manrope, sans-serif;">Visitas – últimos 30 dias</h3>
            <div class="flex items-end gap-1 h-40">
                <div
                    v-for="d in daily" :key="d.date"
                    class="flex-1 flex flex-col items-center gap-1 group"
                >
                    <span class="text-[9px] opacity-0 group-hover:opacity-100 transition-opacity font-bold"
                          style="color: #012d1d; font-family: Manrope, sans-serif;">{{ d.views }}</span>
                    <div
                        class="w-full rounded-t-sm transition-all duration-300 group-hover:opacity-80"
                        :style="`height: ${Math.max((d.views / maxDaily) * 128, d.views > 0 ? 4 : 1)}px; background-color: ${d.views > 0 ? '#012d1d' : '#e5e7eb'};`"
                    ></div>
                    <span class="text-[8px] rotate-45 origin-left" style="color: #9ca3af; font-family: Manrope, sans-serif;">{{ d.date }}</span>
                </div>
            </div>
        </div>

        <!-- ── Top páginas + Países ── -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">

            <!-- Top páginas -->
            <div class="rounded-2xl p-6 bg-white shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-widest mb-5" style="color: #717973; font-family: Manrope, sans-serif;">Páginas mais acessadas</h3>
                <div v-if="top_pages.length" class="space-y-3">
                    <div v-for="(p, i) in top_pages" :key="p.url" class="flex items-center gap-3">
                        <span class="w-5 text-xs font-black text-right shrink-0" style="color: #d1d5db; font-family: Manrope, sans-serif;">{{ i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-sm font-semibold truncate" style="color: #1f2937; font-family: Manrope, sans-serif;">{{ cleanUrl(p.url) }}</span>
                                <span class="text-xs font-bold shrink-0" style="color: #012d1d; font-family: Manrope, sans-serif;">{{ p.views }}</span>
                            </div>
                            <div class="h-1.5 rounded-full" style="background-color: #f0f0f0;">
                                <div class="h-1.5 rounded-full" :style="`width: ${(p.views / top_pages[0].views) * 100}%; background-color: #012d1d;`"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else class="text-sm text-center py-8" style="color: #9ca3af;">Nenhum dado ainda.</p>
            </div>

            <!-- Top países -->
            <div class="rounded-2xl p-6 bg-white shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-widest mb-5" style="color: #717973; font-family: Manrope, sans-serif;">Países</h3>
                <div v-if="top_countries.length" class="space-y-3">
                    <div v-for="(c, i) in top_countries" :key="c.country" class="flex items-center gap-3">
                        <span class="w-5 text-xs font-black text-right shrink-0" style="color: #d1d5db; font-family: Manrope, sans-serif;">{{ i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-sm font-semibold truncate" style="color: #1f2937; font-family: Manrope, sans-serif;">{{ c.country }}</span>
                                <span class="text-xs font-bold shrink-0" style="color: #012d1d; font-family: Manrope, sans-serif;">{{ c.views }}</span>
                            </div>
                            <div class="h-1.5 rounded-full" style="background-color: #f0f0f0;">
                                <div class="h-1.5 rounded-full" :style="`width: ${(c.views / top_countries[0].views) * 100}%; background-color: #fbbf24;`"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else class="text-sm text-center py-8" style="color: #9ca3af;">Aguardando dados de geolocalização.</p>
            </div>
        </div>

        <!-- ── Cidades + Dispositivos + Navegadores ── -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">

            <!-- Cidades -->
            <div class="rounded-2xl p-6 bg-white shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-widest mb-5" style="color: #717973; font-family: Manrope, sans-serif;">Cidades</h3>
                <div v-if="top_cities.length" class="space-y-2.5">
                    <div v-for="c in top_cities" :key="c.city" class="flex items-center justify-between">
                        <div class="min-w-0">
                            <span class="text-sm font-semibold" style="color: #1f2937; font-family: Manrope, sans-serif;">{{ c.city }}</span>
                            <span class="text-xs ml-1" style="color: #9ca3af;">{{ c.country }}</span>
                        </div>
                        <span class="text-xs font-bold ml-2 shrink-0" style="color: #012d1d; font-family: Manrope, sans-serif;">{{ c.views }}</span>
                    </div>
                </div>
                <p v-else class="text-sm text-center py-8" style="color: #9ca3af;">Aguardando dados.</p>
            </div>

            <!-- Dispositivos -->
            <div class="rounded-2xl p-6 bg-white shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-widest mb-5" style="color: #717973; font-family: Manrope, sans-serif;">Dispositivos</h3>
                <div v-if="devices.length" class="space-y-4">
                    <div v-for="d in devices" :key="d.device" class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                             :style="`background-color: ${deviceColors[d.device] ?? '#e5e7eb'}22;`">
                            <span class="material-symbols-outlined text-lg"
                                  :style="`color: ${deviceColors[d.device] ?? '#9ca3af'};`">
                                {{ deviceIcons[d.device] ?? 'devices' }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-semibold capitalize" style="color: #1f2937; font-family: Manrope, sans-serif;">{{ d.device }}</span>
                                <span class="text-xs font-bold" style="color: #6b7280; font-family: Manrope, sans-serif;">
                                    {{ totalDevices > 0 ? Math.round(d.views / totalDevices * 100) : 0 }}%
                                </span>
                            </div>
                            <div class="h-2 rounded-full" style="background-color: #f0f0f0;">
                                <div class="h-2 rounded-full transition-all"
                                     :style="`width: ${totalDevices > 0 ? (d.views / totalDevices * 100) : 0}%; background-color: ${deviceColors[d.device] ?? '#012d1d'};`">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else class="text-sm text-center py-8" style="color: #9ca3af;">Nenhum dado ainda.</p>
            </div>

            <!-- Navegadores -->
            <div class="rounded-2xl p-6 bg-white shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-widest mb-5" style="color: #717973; font-family: Manrope, sans-serif;">Navegadores</h3>
                <div v-if="browsers.length" class="space-y-3">
                    <div v-for="b in browsers" :key="b.browser" class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base" style="color: #9ca3af;">{{ browserIcons[b.browser] ?? 'public' }}</span>
                            <span class="text-sm font-semibold" style="color: #1f2937; font-family: Manrope, sans-serif;">{{ b.browser }}</span>
                        </div>
                        <span class="text-xs font-bold" style="color: #012d1d; font-family: Manrope, sans-serif;">{{ b.views }}</span>
                    </div>
                </div>
                <p v-else class="text-sm text-center py-8" style="color: #9ca3af;">Nenhum dado ainda.</p>
            </div>
        </div>

    </AdminLayout>
</template>
