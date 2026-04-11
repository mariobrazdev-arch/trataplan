<script setup>
import SeoHead from '@/Components/Shared/SeoHead.vue';
import { Link, router } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';
import { computed, ref } from 'vue';

const props = defineProps({
    items: Array,
    productCategories: Array,
    seo: Object,
});

const { locale, t } = useLocale();

// ── Filtro ──────────────────────────────────────────────────────
// null = todos | 'service' = só mão de obra | number = product_category_id
const activeFilter = ref(null);

const categoryFilters = computed(() => [
    {
        key: null,
        label: t('Todos', 'All'),
        icon: 'apps',
        count: props.items?.length ?? 0,
    },
    ...(props.productCategories ?? []).map(cat => ({
        key: cat.id,
        label: cat.name,
        icon: 'category',
        count: (props.items ?? []).filter(i => i.product_category_id === cat.id).length,
    })),
    {
        key: 'service',
        label: t('Mão de Obra', 'Specialized Services'),
        icon: 'construction',
        count: (props.items ?? []).filter(i => i.type === 'service').length,
    },
]);

const filteredItems = computed(() => {
    const all = props.items ?? [];
    if (activeFilter.value === null) return all;
    if (activeFilter.value === 'service') return all.filter(i => i.type === 'service');
    return all.filter(i => i.product_category_id === activeFilter.value);
});

// ── "Carregar mais" paginação client-side ────────────────────────
const pageSize = 12;
const visibleCount = ref(pageSize);
const visibleItems = computed(() => filteredItems.value.slice(0, visibleCount.value));
const hasMore = computed(() => visibleCount.value < filteredItems.value.length);

function loadMore() {
    visibleCount.value += pageSize;
}

// Resetar paginação ao trocar filtro
function setFilter(key) {
    activeFilter.value = key;
    visibleCount.value = pageSize;
}

// ── Fallback de imagem ───────────────────────────────────────────
const fallbacks = [
    'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=70',
    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=70',
    'https://images.unsplash.com/photo-1449158743715-0a90ebb6d2d8?w=800&q=70',
    'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&q=70',
    'https://images.unsplash.com/photo-1597200381847-30ec200eeb9a?w=800&q=70',
    'https://images.unsplash.com/photo-1505409859467-3a796fd5798e?w=800&q=70',
];
function fallbackImg(index) {
    return fallbacks[index % fallbacks.length];
}

// Badge color por tipo
function badgeStyle(item) {
    if (item.type === 'service') {
        return 'background-color:#012d1d; color:#c1ecd4;';
    }
    return 'background-color:#4a3728; color:#f5e6d8;';
}

// URL de detalhe de cada item
function itemUrl(item) {
    const prefix = locale.value === 'en' ? '/en' : '';
    if (item.type === 'service') return `${prefix}/catalog${locale.value === 'en' ? '' : 'o'}/servico${locale.value === 'en' ? '' : ''}/${item.id}`;
    return `${prefix}/${locale.value === 'en' ? 'catalog/product' : 'catalogo/produto'}/${item.id}`;
}
</script>

<template>
    <SeoHead v-bind="seo" />

    <div class="min-h-screen pt-28 pb-24" style="background-color: #fcf9f4;">

        <!-- ── Page header ───────────────────────────────────── -->
        <div class="max-w-7xl mx-auto px-6 mb-10">
            <span class="font-bold tracking-[0.25em] text-xs uppercase block mb-2"
                style="font-family: Manrope, sans-serif; color: #6a5e33;">
                {{ t('CATÁLOGO TRATAPLAN', 'TRATAPLAN CATALOG') }}
            </span>
            <h1 class="text-4xl md:text-5xl font-black leading-tight"
                style="font-family: Manrope, sans-serif; color: #012d1d;">
                {{ t('Soluções em Madeira Tratada', 'Treated Wood Solutions') }}<br>
                <span style="color: #6a5e33;">{{ t('para Grandes Projetos.', 'for Great Projects.') }}</span>
            </h1>
        </div>

        <!-- ── Layout: sidebar + grid ────────────────────────── -->
        <div class="max-w-7xl mx-auto px-6 flex gap-10 items-start">

            <!-- ── Sidebar filtros ──────────────────────────── -->
            <aside class="hidden lg:block w-52 flex-shrink-0 sticky top-32">
                <p class="font-bold tracking-[0.2em] text-[10px] uppercase mb-1"
                    style="font-family: Manrope, sans-serif; color: #9e9e9e;">
                    {{ t('FILTROS DE SELEÇÃO', 'FILTER BY') }}
                </p>
                <p class="text-xs italic mb-5" style="font-family: Newsreader, serif; color: #aaa;">
                    {{ t('Refine sua busca', 'Refine your search') }}
                </p>

                <nav class="space-y-1">
                    <button
                        v-for="filter in categoryFilters"
                        :key="String(filter.key)"
                        @click="setFilter(filter.key)"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition-all duration-200 group"
                        :style="activeFilter === filter.key
                            ? 'background-color: #012d1d; color: #fff;'
                            : 'background-color: transparent; color: #414844;'">
                        <span class="material-symbols-outlined text-sm flex-shrink-0 transition-colors"
                            :style="activeFilter === filter.key ? 'color:#c1ecd4;' : 'color:#9e9e9e;'">
                            {{ filter.icon }}
                        </span>
                        <span class="font-bold text-xs uppercase tracking-widest leading-tight flex-1"
                            style="font-family: Manrope, sans-serif;">
                            {{ filter.label }}
                        </span>
                        <span class="text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center flex-shrink-0"
                            :style="activeFilter === filter.key
                                ? 'background-color: rgba(255,255,255,0.15); color:#fff;'
                                : 'background-color:#ebe8e3; color:#717973;'">
                            {{ filter.count }}
                        </span>
                    </button>
                </nav>

                <!-- Limpar filtros -->
                <button
                    v-if="activeFilter !== null"
                    @click="setFilter(null)"
                    class="mt-6 w-full text-[10px] font-bold uppercase tracking-widest py-2 rounded-lg transition-colors"
                    style="color: #ba1a1a; background-color: #ffdad6;">
                    {{ t('LIMPAR FILTROS', 'CLEAR FILTERS') }}
                </button>
            </aside>

            <!-- ── Main content ─────────────────────────────── -->
            <div class="flex-1 min-w-0">

                <!-- Mobile filter pills -->
                <div class="flex lg:hidden gap-2 flex-wrap mb-6">
                    <button
                        v-for="filter in categoryFilters"
                        :key="String(filter.key)"
                        @click="setFilter(filter.key)"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all"
                        :style="activeFilter === filter.key
                            ? 'background-color:#012d1d; color:#fff;'
                            : 'background-color:#ebe8e3; color:#414844;'">
                        <span class="material-symbols-outlined text-xs">{{ filter.icon }}</span>
                        {{ filter.label }}
                    </button>
                </div>

                <!-- Contador -->
                <p class="text-xs italic mb-6" style="font-family: Newsreader, serif; color: #9e9e9e;">
                    {{ t(
                        `Mostrando ${visibleItems.length} de ${filteredItems.length} soluções`,
                        `Showing ${visibleItems.length} of ${filteredItems.length} solutions`
                    ) }}
                </p>

                <!-- ── Empty state ── -->
                <div v-if="!filteredItems.length" class="py-24 text-center rounded-2xl" style="background-color: #f0ede8;">
                    <span class="material-symbols-outlined text-6xl mb-4 block" style="color: #c1c8c2;">inventory_2</span>
                    <p class="italic" style="font-family: Newsreader, serif; font-size: 1.1rem; color: #9e9e9e;">
                        {{ t('Nenhum item nesta categoria ainda.', 'No items in this category yet.') }}
                    </p>
                </div>

                <!-- ── Cards grid ── -->
                <div v-else
                    class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5"
                    style="grid-auto-rows: min-content;">

                    <article
                        v-for="(item, idx) in visibleItems"
                        :key="`${item.type}-${item.id}`"
                        class="group relative rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-500 cursor-pointer"
                        :class="idx === 0 && activeFilter === null ? 'sm:col-span-2 xl:col-span-1' : ''"
                        style="background-color: #1a1a1a;"
                        @click="router.visit(itemUrl(item))">

                        <!-- Image -->
                        <div class="relative overflow-hidden"
                            :style="idx === 0 && activeFilter === null ? 'height: 420px;' : 'height: 260px;'">
                            <img
                                :src="item.cover_url || fallbackImg(idx)"
                                :alt="item.name"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                loading="lazy"
                            />
                            <!-- Gradient overlay -->
                            <div class="absolute inset-0 transition-opacity duration-500"
                                style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.2) 50%, rgba(0,0,0,0) 100%);"></div>

                            <!-- Arrow on hover -->
                            <div class="absolute top-4 right-4 w-9 h-9 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0"
                                style="background-color: rgba(255,255,255,0.15); backdrop-filter: blur(4px);">
                                <span class="material-symbols-outlined text-white text-base">arrow_outward</span>
                            </div>
                        </div>

                        <!-- Card content -->
                        <div class="absolute bottom-0 left-0 right-0 p-5">
                            <!-- Category badge -->
                            <span class="inline-block px-2.5 py-0.5 rounded text-[9px] font-black uppercase tracking-[0.18em] mb-2"
                                :style="badgeStyle(item)">
                                {{ item.category_label }}
                            </span>
                            <!-- Name -->
                            <h3 class="font-black text-white leading-tight"
                                :class="idx === 0 && activeFilter === null ? 'text-2xl' : 'text-base'"
                                style="font-family: Manrope, sans-serif; text-shadow: 0 1px 4px rgba(0,0,0,0.6);">
                                {{ item.name }}
                            </h3>
                            <!-- Description (only on large first card) -->
                            <p v-if="idx === 0 && activeFilter === null && item.description"
                                class="text-sm mt-1.5 line-clamp-2 text-white/70"
                                style="font-family: Newsreader, serif;">
                                {{ item.description }}
                            </p>
                        </div>
                    </article>
                </div>

                <!-- ── Carregar mais ── -->
                <div v-if="hasMore" class="mt-12 flex flex-col items-center gap-3">
                    <p class="text-sm italic" style="font-family: Newsreader, serif; color: #9e9e9e;">
                        {{ t(
                            `Mostrando ${visibleItems.length} de ${filteredItems.length} soluções`,
                            `Showing ${visibleItems.length} of ${filteredItems.length} solutions`
                        ) }}
                    </p>
                    <button
                        @click="loadMore"
                        class="flex items-center gap-3 px-8 py-3.5 rounded-full font-bold text-sm uppercase tracking-widest border-2 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 active:scale-95"
                        style="font-family: Manrope, sans-serif; border-color: #012d1d; color: #012d1d; background-color: transparent;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#012d1d'; $event.currentTarget.style.color='#fff';"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'; $event.currentTarget.style.color='#012d1d';">
                        {{ t('Carregar Mais Projetos', 'Load More Projects') }}
                        <span class="material-symbols-outlined text-base">expand_more</span>
                    </button>
                </div>

            </div><!-- /main -->
        </div><!-- /layout -->
    </div>
</template>
