<script setup>
import SeoHead from '@/Components/Shared/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';
import { useSettings } from '@/composables/useSettings';

const props = defineProps({
    page: Object,
    seo: Object,
});

const { locale, t } = useLocale();
const { whatsappUrl, logoTransparentUrl } = useSettings();

const features = [
    {
        icon: 'park',
        title_pt: '100% Reflorestamento',
        title_en: '100% Reforestation',
        desc_pt:  'Trabalhamos exclusivamente com Eucalipto e Pinus oriundos de florestas plantadas e manejadas de forma sustentável.',
        desc_en:  'We work exclusively with Eucalyptus and Pine from sustainably managed planted forests.',
    },
    {
        icon: 'precision_manufacturing',
        title_pt: 'Autoclave Vácuo-Pressão',
        title_en: 'Vacuum-Pressure Autoclave',
        desc_pt:  'O processo de impregnação sob alta pressão garante penetração total do preservativo em cada fibra da madeira.',
        desc_en:  'The high-pressure impregnation process ensures total preservative penetration into every wood fiber.',
    },
    {
        icon: 'verified_user',
        title_pt: 'Garantia de 15 Anos',
        title_en: '15-Year Guarantee',
        desc_pt:  'Confiança respaldada por certificação e pelo compromisso com a qualidade que só a experiência pode oferecer.',
        desc_en:  'Confidence backed by certification and the commitment to quality that only experience can offer.',
    },
];
</script>

<template>
    <SeoHead v-bind="seo" />

    <!-- ─── HERO ─────────────────────────────────────────────────────────────── -->
    <section class="relative min-h-[560px] flex items-center overflow-hidden" style="background-color: #012d1d;">
        <!-- Padrão de fundo sutil -->
        <div class="absolute inset-0 opacity-5"
             style="background-image: repeating-linear-gradient(45deg, #fff 0, #fff 1px, transparent 0, transparent 50%); background-size: 20px 20px;"></div>

        <div class="container mx-auto px-6 py-24 grid md:grid-cols-2 gap-12 items-center relative z-10">
            <!-- Texto -->
            <div>
                <span class="inline-block text-xs font-black uppercase tracking-[0.3em] mb-6"
                      style="color: #fbbf24; font-family: Manrope, sans-serif;">
                    {{ t('Sobre a Empresa', 'About the Company') }}
                </span>
                <h1 class="text-white font-headline font-extrabold text-5xl md:text-6xl leading-[1.05] tracking-tight mb-8">
                    {{ page.title || t('Trataplan: A Estrutura da Inovação Sustentável.', 'Trataplan: The Structure of Sustainable Innovation.') }}
                </h1>
                <p class="text-lg leading-relaxed mb-10" style="color: rgba(255,255,255,0.6); font-family: Newsreader, serif; font-style: italic;">
                    {{ t(
                        'Há mais de uma década transformando madeira em durabilidade, no coração do Oeste Catarinense.',
                        'For over a decade transforming wood into durability, in the heart of Western Santa Catarina.'
                    ) }}
                </p>
                <a :href="whatsappUrl" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl font-bold text-sm transition-all active:scale-95 hover:opacity-90"
                   style="background-color: #fbbf24; color: #012d1d; font-family: Manrope, sans-serif;">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    {{ t('Falar com a equipe', 'Talk to our team') }}
                </a>
            </div>

            <!-- Imagem hero -->
            <div class="relative hidden md:block">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl" style="height: 420px;">
                    <img
                        src="https://images.unsplash.com/photo-1448375240586-882707db888b?w=900&q=80"
                        alt="Floresta de pinus – Trataplan"
                        class="w-full h-full object-cover"
                    />
                    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(1,45,29,0.3) 0%, transparent 60%);"></div>
                </div>
                <!-- Badge flutuante -->
                <div class="absolute -bottom-6 -left-6 rounded-2xl px-6 py-5 shadow-xl"
                     style="background-color: #fbbf24;">
                    <p class="font-black text-4xl leading-none" style="color: #012d1d; font-family: Manrope, sans-serif;">+15</p>
                    <p class="text-xs font-bold uppercase tracking-widest mt-1" style="color: #012d1d; font-family: Manrope, sans-serif;">
                        {{ t('Anos de mercado', 'Years in market') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── NOSSA HISTÓRIA ───────────────────────────────────────────────────── -->
    <section class="py-24" style="background-color: #fcf9f4;">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-16 items-center">

                <!-- Imagem -->
                <div class="relative">
                    <img
                        src="https://images.unsplash.com/photo-1586348943529-beaae6c28db9?w=900&q=80"
                        alt="Madeira tratada – Trataplan"
                        class="w-full rounded-2xl shadow-xl object-cover"
                        style="height: 420px;"
                    />
                    <!-- Sobreposição de stats -->
                    <div class="absolute bottom-6 right-6 rounded-xl px-5 py-4 shadow-lg"
                         style="background: rgba(1,45,29,0.92); backdrop-filter: blur(8px);">
                        <p class="font-black text-3xl text-white leading-none" style="font-family: Manrope, sans-serif;">SC-283</p>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.55); font-family: Manrope, sans-serif;">Planalto Alegre – SC</p>
                    </div>
                </div>

                <!-- Texto -->
                <div>
                    <span class="text-xs font-black uppercase tracking-[0.3em] mb-4 block"
                          style="color: #fbbf24; font-family: Manrope, sans-serif;">
                        {{ t('Nossa História', 'Our History') }}
                    </span>
                    <h2 class="font-headline font-extrabold text-4xl mb-6 leading-tight" style="color: #012d1d;">
                        {{ t('Raízes profundas no Oeste Catarinense', 'Deep Roots in Western Santa Catarina') }}
                    </h2>

                    <!-- Conteúdo do banco ou fallback -->
                    <div v-if="page.content"
                         class="prose max-w-none text-lg leading-relaxed"
                         style="color: #4a5568; font-family: Newsreader, serif;"
                         v-html="page.content">
                    </div>
                    <div v-else class="space-y-4 text-lg leading-relaxed" style="color: #4a5568; font-family: Newsreader, serif;">
                        <p>
                            {{ t(
                                'A Trataplan nasceu da visão de oferecer madeira tratada de alta qualidade para o mercado do Oeste Catarinense e de toda a região Sul do Brasil. Nossa missão é entregar durabilidade com responsabilidade ambiental.',
                                'Trataplan was born from the vision of offering high-quality treated wood to the market of Western Santa Catarina and throughout Southern Brazil. Our mission is to deliver durability with environmental responsibility.'
                            ) }}
                        </p>
                        <p>
                            {{ t(
                                'Utilizamos exclusivamente madeiras de reflorestamento — Pinus e Eucalipto — tratadas pelo processo de autoclave vácuo-pressão, o mais eficaz para garantir resistência a cupins, fungos e intempéries por décadas.',
                                'We use exclusively reforestation wood — Pine and Eucalyptus — treated by the vacuum-pressure autoclave process, the most effective for ensuring resistance to termites, fungi and weathering for decades.'
                            ) }}
                        </p>
                    </div>

                    <!-- Stats em linha -->
                    <div class="grid grid-cols-3 gap-6 mt-10 pt-8 border-t" style="border-color: rgba(1,45,29,0.1);">
                        <div>
                            <p class="font-black text-3xl" style="color: #012d1d; font-family: Manrope, sans-serif;">15+</p>
                            <p class="text-xs uppercase tracking-widest mt-1" style="color: #717973; font-family: Manrope, sans-serif;">
                                {{ t('Anos', 'Years') }}
                            </p>
                        </div>
                        <div>
                            <p class="font-black text-3xl" style="color: #012d1d; font-family: Manrope, sans-serif;">100%</p>
                            <p class="text-xs uppercase tracking-widest mt-1" style="color: #717973; font-family: Manrope, sans-serif;">
                                {{ t('Reflorestamento', 'Reforestation') }}
                            </p>
                        </div>
                        <div>
                            <p class="font-black text-3xl" style="color: #012d1d; font-family: Manrope, sans-serif;">SC</p>
                            <p class="text-xs uppercase tracking-widest mt-1" style="color: #717973; font-family: Manrope, sans-serif;">
                                {{ t('Santa Catarina', 'Santa Catarina') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── PROCESSO E QUALIDADE ─────────────────────────────────────────────── -->
    <section class="py-24" style="background-color: #f0ede8;">
        <div class="container mx-auto px-6">

            <!-- Cabeçalho -->
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-black uppercase tracking-[0.3em] mb-4 block"
                      style="color: #fbbf24; font-family: Manrope, sans-serif;">
                    {{ t('Nossa Qualidade', 'Our Quality') }}
                </span>
                <h2 class="font-headline font-extrabold text-4xl md:text-5xl leading-tight" style="color: #012d1d;">
                    {{ t('Processo e Qualidade', 'Process and Quality') }}
                </h2>
                <p class="mt-4 text-lg" style="color: #717973; font-family: Newsreader, serif; font-style: italic;">
                    {{ t(
                        'Cada etapa do nosso processo é pensada para entregar a máxima durabilidade e segurança.',
                        'Every step of our process is designed to deliver maximum durability and safety.'
                    ) }}
                </p>
            </div>

            <!-- Cards de features -->
            <div class="grid md:grid-cols-3 gap-8">
                <div
                    v-for="(f, i) in features"
                    :key="i"
                    class="rounded-2xl p-8 flex flex-col gap-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                    style="background-color: #fff;"
                >
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center shrink-0"
                         style="background-color: #012d1d;">
                        <span class="material-symbols-outlined text-2xl"
                              style="color: #fbbf24; font-variation-settings: 'FILL' 1;">{{ f.icon }}</span>
                    </div>
                    <div>
                        <h3 class="font-headline font-bold text-xl mb-3" style="color: #012d1d;">
                            {{ locale === 'en' ? f.title_en : f.title_pt }}
                        </h3>
                        <p class="leading-relaxed" style="color: #717973; font-family: Newsreader, serif;">
                            {{ locale === 'en' ? f.desc_en : f.desc_pt }}
                        </p>
                    </div>
                    <!-- Número decorativo -->
                    <span class="text-5xl font-black mt-auto self-end leading-none"
                          style="color: rgba(1,45,29,0.07); font-family: Manrope, sans-serif;">
                        0{{ i + 1 }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── CTA — REFLORESTAMENTO ────────────────────────────────────────────── -->
    <section class="relative overflow-hidden py-24" style="background-color: #012d1d;">
        <!-- Gradiente decorativo -->
        <div class="absolute top-0 right-0 w-1/2 h-full opacity-10"
             style="background: radial-gradient(circle at 80% 50%, #fbbf24 0%, transparent 70%);"></div>

        <div class="container mx-auto px-6 grid md:grid-cols-2 gap-12 items-center relative z-10">
            <!-- Texto -->
            <div>
                <h2 class="text-white font-headline font-extrabold text-5xl md:text-6xl leading-[1.05] tracking-tight mb-8">
                    {{ t('Madeira 100% de Reflorestamento.', 'Wood 100% from Reforestation.') }}
                </h2>
                <p class="text-lg leading-relaxed mb-10" style="color: rgba(255,255,255,0.55); font-family: Newsreader, serif; font-style: italic;">
                    {{ t(
                        'Nosso compromisso com o planeta é tão sólido quanto a madeira que produzimos. Cada peça vendida representa uma árvore de floresta plantada.',
                        'Our commitment to the planet is as solid as the wood we produce. Each piece sold represents a tree from a planted forest.'
                    ) }}
                </p>
                <div class="flex flex-wrap gap-4">
                    <a :href="whatsappUrl" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl font-bold text-sm transition-all active:scale-95 hover:opacity-90"
                       style="background-color: #fbbf24; color: #012d1d; font-family: Manrope, sans-serif;">
                        {{ t('Solicitar orçamento', 'Request a quote') }}
                    </a>
                    <Link :href="locale === 'en' ? '/en/products' : '/produtos'"
                          class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl font-bold text-sm border transition-all active:scale-95 hover:bg-white/5"
                          style="border-color: rgba(255,255,255,0.25); color: rgba(255,255,255,0.8); font-family: Manrope, sans-serif;">
                        {{ t('Ver produtos', 'View products') }}
                    </Link>
                </div>
            </div>

            <!-- Imagem circular -->
            <div class="hidden md:flex justify-center">
                <div class="relative">
                    <div class="w-72 h-72 rounded-full overflow-hidden border-4 shadow-2xl"
                         style="border-color: rgba(251,191,36,0.4);">
                        <img
                            src="https://images.unsplash.com/photo-1448375240586-882707db888b?w=600&q=80"
                            alt="Floresta de reflorestamento"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <!-- Anel decorativo -->
                    <div class="absolute -inset-4 rounded-full border opacity-20"
                         style="border-color: #fbbf24;"></div>
                    <!-- Badge eco -->
                    <div class="absolute -bottom-4 -right-4 w-20 h-20 rounded-full flex flex-col items-center justify-center shadow-xl"
                         style="background-color: #fbbf24;">
                        <span class="material-symbols-outlined text-2xl" style="color: #012d1d; font-variation-settings: 'FILL' 1;">eco</span>
                        <span class="text-[9px] font-black uppercase text-center leading-tight mt-0.5" style="color: #012d1d; font-family: Manrope, sans-serif;">
                            ECO<br>CERT
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
