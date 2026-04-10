<script setup>
import SeoHead from '@/Components/Shared/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';

const props = defineProps({
    post: Object,
    prev: Object,
    next: Object,
    seo: Object,
});

const { locale, t } = useLocale();
</script>

<template>
    <SeoHead v-bind="seo" />

    <article class="pt-24 pb-20" style="background-color: #fcf9f4;">
        <!-- Hero image -->
        <div v-if="post.featured_image_url" class="w-full h-72 md:h-96 overflow-hidden mb-12">
            <img :src="post.featured_image_url" :alt="post.title" class="w-full h-full object-cover" />
        </div>

        <div class="container mx-auto px-6 max-w-3xl">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm mb-8" style="font-family: Manrope, sans-serif; color: #717973;">
                <Link :href="locale === 'en' ? '/en' : '/'" class="hover:underline">{{ t('Início', 'Home') }}</Link>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <Link :href="locale === 'en' ? '/en/blog' : '/blog'" class="hover:underline">Blog</Link>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <span class="truncate max-w-48">{{ post.title }}</span>
            </nav>

            <!-- Meta -->
            <div class="flex items-center gap-4 mb-6">
                <span class="text-xs uppercase tracking-widest font-bold" style="font-family: Manrope, sans-serif; color: #6a5e33;">
                    {{ post.published_at }}
                </span>
                <span v-if="post.author" class="text-xs" style="font-family: Manrope, sans-serif; color: #717973;">
                    por {{ post.author }}
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-bold mb-10 leading-tight" style="font-family: Manrope, sans-serif; color: #012d1d;">
                {{ post.title }}
            </h1>

            <!-- Content -->
            <div
                class="prose-trataplan text-lg leading-relaxed"
                style="font-family: Newsreader, serif; color: #1c1c19; max-width: none;"
                v-html="post.content"
            ></div>

            <!-- Navigation -->
            <div class="mt-16 pt-8 grid grid-cols-1 sm:grid-cols-2 gap-6" style="border-top: 1px solid #e5e2dd;">
                <Link
                    v-if="prev"
                    :href="(locale === 'en' ? '/en/blog/' : '/blog/') + prev.slug"
                    class="flex items-start gap-3 group"
                >
                    <span class="material-symbols-outlined text-2xl mt-0.5 group-hover:-translate-x-1 transition-transform" style="color: #6a5e33;">arrow_back</span>
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-1" style="font-family: Manrope, sans-serif; color: #717973;">{{ t('Anterior', 'Previous') }}</p>
                        <p class="font-bold group-hover:underline" style="font-family: Manrope, sans-serif; color: #012d1d;">{{ prev.title }}</p>
                    </div>
                </Link>
                <Link
                    v-if="next"
                    :href="(locale === 'en' ? '/en/blog/' : '/blog/') + next.slug"
                    class="flex items-end justify-end gap-3 group text-right ml-auto"
                >
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-1" style="font-family: Manrope, sans-serif; color: #717973;">{{ t('Próximo', 'Next') }}</p>
                        <p class="font-bold group-hover:underline" style="font-family: Manrope, sans-serif; color: #012d1d;">{{ next.title }}</p>
                    </div>
                    <span class="material-symbols-outlined text-2xl mt-0.5 group-hover:translate-x-1 transition-transform" style="color: #6a5e33;">arrow_forward</span>
                </Link>
            </div>
        </div>
    </article>
</template>
