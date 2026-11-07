<script setup>
import SeoHead from '@/Components/Shared/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    posts: Object,          // LengthAwarePaginator
    postCategories: Array,
    seo: Object,
});

// ── Filtro de categoria (client-side dentro da página atual) ────
const activeCategory = ref(null);

const filteredPosts = computed(() => {
    const all = props.posts?.data ?? [];
    if (activeCategory.value === null) return all;
    return all.filter(p => p.post_category_id === activeCategory.value);
});

// ── Busca ───────────────────────────────────────────────────────
const search = ref('');

const visiblePosts = computed(() => {
    if (!search.value.trim()) return filteredPosts.value;
    const q = search.value.toLowerCase();
    return filteredPosts.value.filter(p =>
        p.title?.toLowerCase().includes(q) ||
        p.excerpt?.toLowerCase().includes(q)
    );
});

// ── Layout: destaque + lateral + resto ─────────────────────────
const featuredPost   = computed(() => visiblePosts.value[0] ?? null);
const secondPost     = computed(() => visiblePosts.value[1] ?? null);
const remainingPosts = computed(() => visiblePosts.value.slice(2));

const pagination = computed(() => props.posts);

const fallback = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=900&q=70';

function postUrl(post) {
    return `/blog/${post.slug}`;
}
</script>

<template>
    <SeoHead v-bind="seo" />

    <div class="min-h-screen pt-28 pb-24" style="background-color: #fcf9f4;">
        <div class="max-w-6xl mx-auto px-6">

            <!-- ── Page header ──────────────────────────────────── -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <div>
                    <span class="block font-bold tracking-[0.25em] text-xs uppercase mb-3"
                        style="font-family: Manrope, sans-serif; color: #6a5e33;">
                        {{ 'DIÁRIO DE CAMPO' }}
                    </span>
                    <h1 class="text-4xl md:text-5xl font-black leading-tight"
                        style="font-family: Manrope, sans-serif; color: #012d1d;">
                        {{ 'Novidades, Dicas' }}<br>
                        <span style="color: #6a5e33;">{{ 'e Bastidores.' }}</span>
                    </h1>
                </div>
                <div class="flex items-end">
                    <p class="text-lg leading-relaxed font-body italic"
                        style="font-family: Newsreader, serif; color: #5d4c40;">
                        {{ 'Explore projetos, técnicas sustentáveis e novidades sobre madeira tratada e paisagismo.' }}
                    </p>
                </div>
            </div>

            <!-- ── Filtros + busca ──────────────────────────────── -->
            <div class="flex flex-wrap items-center gap-3 mb-10">
                <button
                    @click="activeCategory = null"
                    class="px-5 py-2 rounded-full text-sm font-bold transition-all"
                    style="font-family: Manrope, sans-serif;"
                    :style="activeCategory === null
                        ? 'background-color:#012d1d; color:#fff;'
                        : 'background-color:#ebe8e3; color:#414844;'">
                    {{ 'Todas' }}
                </button>
                <button
                    v-for="cat in postCategories"
                    :key="cat.id"
                    @click="activeCategory = cat.id"
                    class="px-5 py-2 rounded-full text-sm font-bold transition-all"
                    style="font-family: Manrope, sans-serif;"
                    :style="activeCategory === cat.id
                        ? 'background-color:#012d1d; color:#fff;'
                        : 'background-color:#ebe8e3; color:#414844;'">
                    {{ cat.name }}
                </button>

                <div class="flex-1 hidden sm:block"></div>

                <!-- Busca -->
                <div class="flex items-center gap-2 px-4 py-2 rounded-full w-full sm:w-auto"
                    style="background-color:#ebe8e3; min-width:200px;">
                    <span class="material-symbols-outlined text-base" style="color:#9e9e9e;">search</span>
                    <input
                        v-model="search"
                        type="text"
                        :placeholder="'Buscar artigo...'"
                        class="bg-transparent outline-none text-sm flex-1"
                        style="font-family: Manrope, sans-serif; color: #414844;" />
                </div>
            </div>

            <!-- ── Sem resultados ───────────────────────────────── -->
            <div v-if="!visiblePosts.length" class="py-24 text-center">
                <span class="material-symbols-outlined text-5xl block mb-3" style="color:#c1c8c2;">article</span>
                <p class="font-body italic text-lg" style="color:#9e9e9e;">
                    {{ 'Nenhuma postagem encontrada.' }}
                </p>
            </div>

            <!-- ── Grid de posts ────────────────────────────────── -->
            <div v-else class="space-y-6">

                <!-- Linha 1: destaque grande (2/3) + card lateral (1/3) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Post destaque -->
                    <Link v-if="featuredPost"
                        :href="postUrl(featuredPost)"
                        class="md:col-span-2 relative rounded-2xl overflow-hidden group block"
                        style="min-height: 420px; background-color:#012d1d;">
                        <img
                            :src="featuredPost.featured_image_url || fallback"
                            :alt="featuredPost.title"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            @error="$event.target.src = fallback" />
                        <div class="absolute inset-0"
                            style="background: linear-gradient(to top, rgba(1,45,29,0.90) 0%, rgba(1,45,29,0.25) 55%, transparent 100%);"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-7">
                            <span v-if="featuredPost.category_label"
                                class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest mb-4"
                                style="font-family:Manrope,sans-serif; background-color:rgba(255,255,255,0.15); color:#c1ecd4; backdrop-filter:blur(4px);">
                                {{ featuredPost.category_label }}
                            </span>
                            <h2 class="text-2xl md:text-3xl font-black text-white leading-snug mb-3"
                                style="font-family:Manrope,sans-serif;">
                                {{ featuredPost.title }}
                            </h2>
                            <p v-if="featuredPost.excerpt"
                                class="text-sm text-white/75 leading-relaxed mb-5 line-clamp-2"
                                style="font-family:Newsreader,serif;">
                                {{ featuredPost.excerpt }}
                            </p>
                            <span class="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-wider"
                                style="font-family:Manrope,sans-serif; color:#c1ecd4;">
                                {{ 'Ler artigo completo' }}
                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                            </span>
                        </div>
                    </Link>

                    <!-- Card lateral -->
                    <Link v-if="secondPost"
                        :href="postUrl(secondPost)"
                        class="rounded-2xl overflow-hidden group flex flex-col"
                        style="background-color:#ffffff;">
                        <div class="relative overflow-hidden" style="height:200px;">
                            <img
                                :src="secondPost.featured_image_url || fallback"
                                :alt="secondPost.title"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                @error="$event.target.src = fallback" />
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <span v-if="secondPost.category_label"
                                class="text-[10px] font-bold uppercase tracking-[0.2em] mb-2 block"
                                style="font-family:Manrope,sans-serif; color:#6a5e33;">
                                {{ secondPost.category_label }}
                            </span>
                            <h3 class="text-lg font-extrabold leading-snug mb-2"
                                style="font-family:Manrope,sans-serif; color:#012d1d;">
                                {{ secondPost.title }}
                            </h3>
                            <p v-if="secondPost.excerpt"
                                class="text-sm leading-relaxed line-clamp-3 flex-1"
                                style="font-family:Newsreader,serif; color:#5d4c40;">
                                {{ secondPost.excerpt }}
                            </p>
                            <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold uppercase tracking-widest"
                                style="font-family:Manrope,sans-serif; color:#012d1d;">
                                {{ 'Explorar notícia' }}
                                <span class="material-symbols-outlined text-sm transition-transform group-hover:translate-x-1">arrow_forward</span>
                            </span>
                        </div>
                    </Link>
                </div>

                <!-- Linhas seguintes: grid de 3 -->
                <div v-if="remainingPosts.length"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <Link
                        v-for="post in remainingPosts"
                        :key="post.id"
                        :href="postUrl(post)"
                        class="rounded-2xl overflow-hidden group flex flex-col"
                        style="background-color:#ffffff;">
                        <div class="relative overflow-hidden" style="height:190px;">
                            <img
                                :src="post.featured_image_url || fallback"
                                :alt="post.title"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                @error="$event.target.src = fallback" />
                        </div>
                        <div class="p-5 flex flex-col flex-1">
                            <span v-if="post.category_label"
                                class="text-[10px] font-bold uppercase tracking-[0.2em] mb-2 block"
                                style="font-family:Manrope,sans-serif; color:#6a5e33;">
                                {{ post.category_label }}
                            </span>
                            <h3 class="text-base font-extrabold leading-snug mb-2"
                                style="font-family:Manrope,sans-serif; color:#012d1d;">
                                {{ post.title }}
                            </h3>
                            <p v-if="post.excerpt"
                                class="text-sm leading-relaxed line-clamp-3 flex-1"
                                style="font-family:Newsreader,serif; color:#5d4c40;">
                                {{ post.excerpt }}
                            </p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-[10px] font-label uppercase tracking-widest"
                                    style="color:#9e9e9e;">{{ post.published_at }}</span>
                                <span class="material-symbols-outlined text-base transition-transform group-hover:translate-x-1"
                                    style="color:#012d1d;">arrow_forward</span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- ── Paginação ────────────────────────────────────── -->
            <div v-if="pagination && pagination.last_page > 1"
                class="mt-14 flex items-center justify-between">
                <p class="text-sm" style="font-family:Manrope,sans-serif; color:#9e9e9e;">
                    {{ 'Página' }}
                    <strong style="color:#012d1d;">{{ String(pagination.current_page).padStart(2,'0') }}</strong>
                    {{ 'de' }}
                    <strong style="color:#012d1d;">{{ String(pagination.last_page).padStart(2,'0') }}</strong>
                </p>

                <div class="flex items-center gap-2">
                    <Link
                        v-if="pagination.prev_page_url"
                        :href="pagination.prev_page_url"
                        class="w-10 h-10 flex items-center justify-center rounded-full transition-all"
                        style="background-color:#ebe8e3; color:#012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#012d1d'; $event.currentTarget.style.color='#fff'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='#ebe8e3'; $event.currentTarget.style.color='#012d1d'">
                        <span class="material-symbols-outlined text-base">chevron_left</span>
                    </Link>
                    <span v-else class="w-10 h-10 flex items-center justify-center rounded-full opacity-30"
                        style="background-color:#ebe8e3; color:#012d1d;">
                        <span class="material-symbols-outlined text-base">chevron_left</span>
                    </span>

                    <template v-for="link in pagination.links" :key="link.label">
                        <Link
                            v-if="link.url && !link.label.includes('&laquo;') && !link.label.includes('&raquo;')"
                            :href="link.url"
                            class="w-10 h-10 flex items-center justify-center rounded-full text-sm font-bold transition-all"
                            style="font-family:Manrope,sans-serif;"
                            :style="link.active ? 'background-color:#012d1d; color:#fff;' : 'background-color:#ebe8e3; color:#414844;'">
                            {{ link.label }}
                        </Link>
                        <span
                            v-else-if="!link.url && !link.label.includes('&laquo;') && !link.label.includes('&raquo;')"
                            class="w-10 h-10 flex items-center justify-center rounded-full text-sm font-bold"
                            style="font-family:Manrope,sans-serif; background-color:#012d1d; color:#fff;">
                            {{ link.label }}
                        </span>
                    </template>

                    <Link
                        v-if="pagination.next_page_url"
                        :href="pagination.next_page_url"
                        class="w-10 h-10 flex items-center justify-center rounded-full transition-all"
                        style="background-color:#ebe8e3; color:#012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#012d1d'; $event.currentTarget.style.color='#fff'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='#ebe8e3'; $event.currentTarget.style.color='#012d1d'">
                        <span class="material-symbols-outlined text-base">chevron_right</span>
                    </Link>
                    <span v-else class="w-10 h-10 flex items-center justify-center rounded-full opacity-30"
                        style="background-color:#ebe8e3; color:#012d1d;">
                        <span class="material-symbols-outlined text-base">chevron_right</span>
                    </span>
                </div>
            </div>

        </div>
    </div>
</template>
