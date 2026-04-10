<script setup>
import SeoHead from '@/Components/Shared/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';

const props = defineProps({
    posts: Object,
    seo: Object,
});

const { locale, t } = useLocale();
</script>

<template>
    <SeoHead v-bind="seo" />

    <div class="pt-28 pb-20" style="background-color: #fcf9f4;">
        <div class="container mx-auto px-6">
            <!-- Header -->
            <div class="text-center mb-16">
                <span class="font-bold tracking-widest text-sm uppercase mb-3 block" style="font-family: Manrope, sans-serif; color: #6a5e33;">Blog</span>
                <h1 class="text-4xl md:text-5xl font-bold" style="font-family: Manrope, sans-serif; color: #012d1d;">
                    {{ t('Dicas e Novidades', 'Tips & News') }}
                </h1>
                <div class="w-24 h-1 mx-auto mt-6" style="background-color: #6a5e33;"></div>
            </div>

            <!-- Empty state -->
            <div v-if="!posts?.data?.length" class="text-center py-24">
                <span class="material-symbols-outlined text-6xl mb-4 block" style="color: #c1c8c2;">article</span>
                <p style="font-family: Newsreader, serif; color: #717973;">
                    {{ t('Nenhuma publicação ainda.', 'No posts yet.') }}
                </p>
            </div>

            <!-- Posts grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                <article v-for="post in posts.data" :key="post.id" class="flex flex-col rounded-xl overflow-hidden" style="background-color: #ffffff;">
                    <Link :href="(locale === 'en' ? '/en/blog/' : '/blog/') + post.slug">
                        <div class="h-48 overflow-hidden">
                            <img
                                v-if="post.featured_image_url"
                                :src="post.featured_image_url"
                                :alt="post.title"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center" style="background-color: #f0ede8;">
                                <span class="material-symbols-outlined text-5xl" style="color: #c1c8c2;">article</span>
                            </div>
                        </div>
                    </Link>
                    <div class="p-6 flex flex-col flex-grow">
                        <span class="text-xs uppercase tracking-widest mb-2" style="font-family: Manrope, sans-serif; color: #6a5e33;">{{ post.published_at }}</span>
                        <Link :href="(locale === 'en' ? '/en/blog/' : '/blog/') + post.slug">
                            <h2 class="font-bold text-xl mb-3 hover:opacity-70 transition-colors leading-tight" style="font-family: Manrope, sans-serif; color: #012d1d;">
                                {{ post.title }}
                            </h2>
                        </Link>
                        <p class="text-sm leading-relaxed flex-grow" style="font-family: Newsreader, serif; color: #414844;">{{ post.excerpt }}</p>
                        <Link
                            :href="(locale === 'en' ? '/en/blog/' : '/blog/') + post.slug"
                            class="mt-4 inline-flex items-center gap-1 font-bold text-sm hover:gap-2 transition-all"
                            style="font-family: Manrope, sans-serif; color: #012d1d;"
                        >
                            {{ t('Ler mais', 'Read more') }}
                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                        </Link>
                    </div>
                </article>
            </div>

            <!-- Pagination -->
            <div v-if="posts?.links?.length > 3" class="flex justify-center gap-2 flex-wrap">
                <Link
                    v-for="link in posts.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all"
                    :class="link.active
                        ? 'text-white'
                        : link.url
                            ? 'hover:opacity-80'
                            : 'opacity-30 cursor-not-allowed'"
                    :style="link.active
                        ? 'font-family: Manrope, sans-serif; background-color: #012d1d; color: white;'
                        : 'font-family: Manrope, sans-serif; background-color: #f0ede8; color: #012d1d;'"
                />
            </div>
        </div>
    </div>
</template>
