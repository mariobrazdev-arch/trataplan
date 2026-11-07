<script setup>
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    items: { type: Array, default: () => [] },
    urlPrefix: { type: String, required: true },
    fallbacks: { type: Array, default: () => [] },
    emptyIcon: { type: String, default: 'photo_library' },
    emptyText: { type: String, default: '' },
});

const pageSize = 12;
const visibleCount = ref(pageSize);
const visibleItems = computed(() => props.items.slice(0, visibleCount.value));
const hasMore = computed(() => visibleCount.value < props.items.length);

function loadMore() {
    visibleCount.value += pageSize;
}

function fallbackImg(index) {
    if (!props.fallbacks.length) return null;
    return props.fallbacks[index % props.fallbacks.length];
}

function itemUrl(item) {
    return `${props.urlPrefix}/${item.id}`;
}
</script>

<template>
    <div>
        <!-- ── Empty state ── -->
        <div v-if="!visibleItems.length" class="py-24 text-center rounded-2xl" style="background-color: #f0ede8;">
            <span class="material-symbols-outlined text-6xl mb-4 block" style="color: #c1c8c2;">{{ emptyIcon }}</span>
            <p class="italic" style="font-family: Newsreader, serif; font-size: 1.1rem; color: #9e9e9e;">
                {{ emptyText }}
            </p>
        </div>

        <!-- ── Cards grid ── -->
        <div v-else
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">

            <article
                v-for="(item, idx) in visibleItems"
                :key="item.id"
                class="group relative rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-500 cursor-pointer"
                style="background-color: #1a1a1a;"
                @click="router.visit(itemUrl(item))">

                <div class="relative overflow-hidden aspect-[4/5]">
                    <img
                        :src="item.cover_url || fallbackImg(idx)"
                        :alt="item.name"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 transition-opacity duration-500"
                        style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.1) 55%, rgba(0,0,0,0) 100%);"></div>

                    <div class="absolute top-4 right-4 w-9 h-9 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0"
                        style="background-color: rgba(255,255,255,0.15); backdrop-filter: blur(4px);">
                        <span class="material-symbols-outlined text-white text-base">arrow_outward</span>
                    </div>
                </div>

                <div class="absolute bottom-0 left-0 right-0 p-5">
                    <h3 class="font-black text-white leading-tight text-xl"
                        style="font-family: Manrope, sans-serif; text-shadow: 0 1px 4px rgba(0,0,0,0.6);">
                        {{ item.name }}
                    </h3>
                    <p v-if="item.photos_count" class="text-xs mt-1 text-white/70 flex items-center gap-1"
                        style="font-family: Manrope, sans-serif;">
                        <span class="material-symbols-outlined text-sm">photo_library</span>
                        {{ item.photos_count }} fotos
                    </p>
                </div>
            </article>
        </div>

        <!-- ── Carregar mais ── -->
        <div v-if="hasMore" class="mt-12 flex flex-col items-center gap-3">
            <button
                @click="loadMore"
                class="flex items-center gap-3 px-8 py-3.5 rounded-full font-bold text-sm uppercase tracking-widest border-2 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 active:scale-95"
                style="font-family: Manrope, sans-serif; border-color: #012d1d; color: #012d1d; background-color: transparent;"
                @mouseenter="$event.currentTarget.style.backgroundColor='#012d1d'; $event.currentTarget.style.color='#fff';"
                @mouseleave="$event.currentTarget.style.backgroundColor='transparent'; $event.currentTarget.style.color='#012d1d';">
                Carregar Mais
                <span class="material-symbols-outlined text-base">expand_more</span>
            </button>
        </div>
    </div>
</template>
