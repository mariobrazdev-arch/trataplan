<script setup>
import SeoHead from '@/Components/Shared/SeoHead.vue';

const props = defineProps({
    page: Object,
    seo: Object,
});
</script>

<template>
    <SeoHead v-bind="seo" />

    <div class="pt-24 pb-20" style="background-color: #fcf9f4;">
        <div class="container mx-auto px-6 max-w-4xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-8" style="font-family: Manrope, sans-serif; color: #012d1d;">
                {{ page.title }}
            </h1>
            <div
                v-if="page.content"
                class="prose-trataplan text-lg leading-relaxed"
                v-html="page.content"
            ></div>
        </div>
    </div>
</template>
