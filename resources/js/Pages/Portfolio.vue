<script setup>
import SeoHead from '@/Components/Shared/SeoHead.vue';
import PortfolioGrid from '@/Components/Shared/PortfolioGrid.vue';

defineProps({
    items: { type: Array, default: () => [] },
    workforceItems: { type: Array, default: () => [] },
    seo: Object,
});

const productFallbacks = [
    'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=70',
    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=70',
    'https://images.unsplash.com/photo-1449158743715-0a90ebb6d2d8?w=800&q=70',
    'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&q=70',
    'https://images.unsplash.com/photo-1597200381847-30ec200eeb9a?w=800&q=70',
    'https://images.unsplash.com/photo-1505409859467-3a796fd5798e?w=800&q=70',
];

const workforceFallbacks = [
    'https://images.unsplash.com/photo-1449158743715-0a90ebb6d2d8?w=800&q=70',
    'https://images.unsplash.com/photo-1597200381847-30ec200eeb9a?w=800&q=70',
    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=70',
    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=70',
];
</script>

<template>
    <SeoHead v-bind="seo" />

    <div class="min-h-screen pt-28 pb-24" style="background-color: #fcf9f4;">

        <!-- ── Seção: Portfólio (Produtos) ──────────────────────── -->
        <div class="max-w-7xl mx-auto px-6 mb-12">
            <span class="font-bold tracking-[0.25em] text-xs uppercase block mb-2"
                style="font-family: Manrope, sans-serif; color: #6a5e33;">
                {{ 'PORTFÓLIO TRATAPLAN' }}
            </span>
            <h1 class="text-4xl md:text-5xl font-black leading-tight"
                style="font-family: Manrope, sans-serif; color: #012d1d;">
                {{ 'Nosso Trabalho em' }}<br>
                <span style="color: #6a5e33;">{{ 'Madeira Tratada.' }}</span>
            </h1>
        </div>

        <div class="max-w-7xl mx-auto px-6 mb-20">
            <PortfolioGrid
                :items="items"
                url-prefix="/portfolio"
                :fallbacks="productFallbacks"
                empty-icon="photo_library"
                :empty-text="'Nenhum item cadastrado ainda.'"
            />
        </div>

        <!-- ── Seção: Mão de Obra Especializada ─────────────────── -->
        <div class="max-w-7xl mx-auto px-6 mb-12">
            <span class="font-bold tracking-[0.25em] text-xs uppercase block mb-2"
                style="font-family: Manrope, sans-serif; color: #6a5e33;">
                {{ 'MÃO DE OBRA TRATAPLAN' }}
            </span>
            <h2 class="text-4xl md:text-5xl font-black leading-tight"
                style="font-family: Manrope, sans-serif; color: #012d1d;">
                {{ 'Mão de Obra' }}<br>
                <span style="color: #6a5e33;">{{ 'Especializada.' }}</span>
            </h2>
        </div>

        <div class="max-w-7xl mx-auto px-6">
            <PortfolioGrid
                :items="workforceItems"
                url-prefix="/mao-de-obra"
                :fallbacks="workforceFallbacks"
                empty-icon="construction"
                :empty-text="'Nenhum serviço cadastrado ainda.'"
            />
        </div>

    </div>
</template>
