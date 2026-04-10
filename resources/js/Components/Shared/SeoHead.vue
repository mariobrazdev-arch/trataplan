<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';

const props = defineProps({
    title:       { type: String, default: 'Trataplan – Madeira Tratada' },
    description: { type: String, default: 'Referência em madeira tratada no Oeste Catarinense. Eucalipto e Pinus Autoclavado com qualidade e durabilidade.' },
    image:       { type: String, default: null },
    canonical:   { type: String, default: null },
    type:        { type: String, default: 'website' }, // website | article
    noAlternate: { type: Boolean, default: false },
});

const siteName = 'Trataplan';
const siteUrl  = 'https://trataplan.com.br';

const page      = usePage();
const { locale } = useLocale();

const fullTitle = computed(() =>
    props.title.includes('Trataplan') ? props.title : `${props.title} | ${siteName}`
);

const canonicalUrl = computed(() =>
    props.canonical ? props.canonical : (siteUrl + page.url)
);

const ogLocale = computed(() => locale.value === 'en' ? 'en_US' : 'pt_BR');

// hreflang alternate URLs
const altUrls = computed(() => {
    if (props.noAlternate) return null;
    const path = page.url;
    if (locale.value === 'en') {
        const ptPath = path.replace(/^\/en/, '') || '/';
        return { pt: siteUrl + ptPath, en: siteUrl + path };
    } else {
        const enPath = '/en' + (path === '/' ? '' : path);
        return { pt: siteUrl + path, en: siteUrl + enPath };
    }
});

const ogImage = computed(() => props.image || (siteUrl + '/og-default.jpg'));
</script>

<template>
    <Head>
        <title>{{ fullTitle }}</title>
        <meta name="description" :content="description" />
        <link rel="canonical" :href="canonicalUrl" />

        <!-- hreflang — bilingual SEO -->
        <template v-if="altUrls">
            <link rel="alternate" hreflang="pt-BR" :href="altUrls.pt" />
            <link rel="alternate" hreflang="en"    :href="altUrls.en" />
            <link rel="alternate" hreflang="x-default" :href="altUrls.pt" />
        </template>

        <!-- Open Graph -->
        <meta property="og:type"        :content="type" />
        <meta property="og:url"         :content="canonicalUrl" />
        <meta property="og:title"       :content="fullTitle" />
        <meta property="og:description" :content="description" />
        <meta property="og:image"       :content="ogImage" />
        <meta property="og:site_name"   :content="siteName" />
        <meta property="og:locale"      :content="ogLocale" />

        <!-- Twitter Card -->
        <meta name="twitter:card"        content="summary_large_image" />
        <meta name="twitter:title"       :content="fullTitle" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image"       :content="ogImage" />
    </Head>
</template>
