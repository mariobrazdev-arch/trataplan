<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';
import { useSettings } from '@/composables/useSettings';

const { locale, t } = useLocale();
const { setting, whatsappUrl, logoTransparentUrl } = useSettings();

const prefix = computed(() => locale.value === 'en' ? '/en' : '');

const navLinks = computed(() => [
    { label: t('Início', 'Home'),        href: prefix.value + '/' },
    { label: t('Sobre', 'About'),        href: prefix.value + (locale.value === 'en' ? '/about'    : '/sobre') },
    { label: t('Produtos', 'Products'),  href: prefix.value + (locale.value === 'en' ? '/products' : '/produtos') },
    { label: t('Parceiros', 'Partners'), href: prefix.value + (locale.value === 'en' ? '/partners' : '/parceiros') },
    { label: 'Blog',                     href: prefix.value + '/blog' },
    { label: t('Contato', 'Contact'),    href: prefix.value + (locale.value === 'en' ? '/contact'  : '/contato') },
]);

const productLinks = computed(() => [
    { label: t('Decks & Pergolados', 'Decks & Pergolas'),  href: prefix.value + (locale.value === 'en' ? '/products' : '/produtos') },
    { label: t('Construção Civil', 'Civil Construction'),   href: prefix.value + (locale.value === 'en' ? '/products' : '/produtos') },
    { label: t('Cercas e Currais', 'Fences & Pens'),       href: prefix.value + (locale.value === 'en' ? '/products' : '/produtos') },
    { label: t('Paisagismo', 'Landscaping'),               href: prefix.value + (locale.value === 'en' ? '/products' : '/produtos') },
]);

const currentYear = new Date().getFullYear();

const companyUnits = computed(() => {
    const raw = setting('company_units', null);
    if (raw) {
        try { return JSON.parse(raw); } catch { /* fall through */ }
    }
    const fallback = setting('contact_address', 'SC-283, Planalto Alegre - SC, 89882-000');
    return fallback ? [{ label: '', address: fallback }] : [];
});
const phone     = computed(() => setting('contact_phone',   '+55 49 9960-0329'));
const phoneRaw  = computed(() => phone.value.replace(/\D/g, ''));
const email     = computed(() => setting('contact_email',   'trataplan@trataplan.com.br'));
const instagram = computed(() => setting('instagram_url',   ''));
const facebook  = computed(() => setting('facebook_url',    ''));
const linkedin  = computed(() => setting('linkedin_url',    ''));
</script>

<template>
    <footer
        class="w-full"
        style="background-color: #011a10;"
        itemscope
        itemtype="https://schema.org/LocalBusiness"
    >
        <!-- Main grid -->
        <div class="max-w-7xl mx-auto px-6 pt-16 pb-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12 pb-12 border-b"
                 style="border-color: rgba(255,255,255,0.08);">

                <!-- Col 1: Brand -->
                <div class="sm:col-span-2 lg:col-span-1">
                    <Link :href="locale === 'en' ? '/en' : '/'" class="inline-flex items-center gap-2.5 mb-5">
                        <img
                            v-if="logoTransparentUrl"
                            :src="logoTransparentUrl"
                            alt="Trataplan"
                            class="h-16 w-auto object-contain"
                            itemprop="logo"
                        />
                        <template v-else>
                            <span class="material-symbols-outlined text-amber-400 text-2xl leading-none"
                                  style="font-variation-settings: 'FILL' 1;">forest</span>
                            <span class="text-xl font-black tracking-tight text-white"
                                  style="font-family: Manrope, sans-serif; letter-spacing: -0.02em;" itemprop="name">TRATAPLAN</span>
                        </template>
                    </Link>

                    <p class="text-sm leading-relaxed mb-6"
                       style="color: rgba(255,255,255,0.55); font-family: Newsreader, serif; font-style: italic;">
                        {{ setting('footer_text_pt', 'Referência em tratamento de madeiras no Oeste Catarinense. Qualidade e compromisso com o meio ambiente em cada peça.') }}
                    </p>

                    <!-- Social icons -->
                    <div class="flex items-center gap-2">
                        <a v-if="instagram"
                           :href="instagram"
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110 hover:bg-white/15"
                           style="background: rgba(255,255,255,0.08);"
                           aria-label="Instagram">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
                                 style="color: rgba(255,255,255,0.75);">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                <circle cx="12" cy="12" r="4"/>
                                <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor" stroke="none"/>
                            </svg>
                        </a>
                        <a v-if="facebook"
                           :href="facebook"
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110 hover:bg-white/15"
                           style="background: rgba(255,255,255,0.08);"
                           aria-label="Facebook">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"
                                 style="color: rgba(255,255,255,0.75);">
                                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                            </svg>
                        </a>
                        <a v-if="linkedin"
                           :href="linkedin"
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110 hover:bg-white/15"
                           style="background: rgba(255,255,255,0.08);"
                           aria-label="LinkedIn">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"
                                 style="color: rgba(255,255,255,0.75);">
                                <path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/>
                                <circle cx="4" cy="4" r="2"/>
                            </svg>
                        </a>
                        <a :href="whatsappUrl"
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110 hover:bg-white/15"
                           style="background: rgba(255,255,255,0.08);"
                           aria-label="WhatsApp">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"
                                 style="color: rgba(255,255,255,0.75);">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Navegação -->
                <div>
                    <span class="block text-xs font-bold uppercase tracking-widest mb-5"
                          style="color: rgba(255,255,255,0.4); font-family: Manrope, sans-serif;">
                        {{ t('Navegação', 'Navigation') }}
                    </span>
                    <ul class="space-y-3">
                        <li v-for="link in navLinks" :key="link.href">
                            <Link
                                :href="link.href"
                                class="text-sm text-white/60 hover:text-white transition-colors duration-150"
                                style="font-family: Manrope, sans-serif;"
                            >
                                {{ link.label }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Produtos -->
                <div>
                    <span class="block text-xs font-bold uppercase tracking-widest mb-5"
                          style="color: rgba(255,255,255,0.4); font-family: Manrope, sans-serif;">
                        {{ t('Produtos', 'Products') }}
                    </span>
                    <ul class="space-y-3">
                        <li v-for="link in productLinks" :key="link.label">
                            <Link
                                :href="link.href"
                                class="text-sm text-white/60 hover:text-white transition-colors duration-150"
                                style="font-family: Newsreader, serif; font-style: italic;"
                            >
                                {{ link.label }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Col 4: Contato -->
                <div>
                    <span class="block text-xs font-bold uppercase tracking-widest mb-5"
                          style="color: rgba(255,255,255,0.4); font-family: Manrope, sans-serif;">
                        {{ t('Contato', 'Contact') }}
                    </span>
                    <address class="not-italic space-y-4" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                        <div v-for="(unit, i) in companyUnits" :key="i" class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-amber-400/80 text-base mt-0.5 shrink-0">location_on</span>
                            <div>
                                <p v-if="unit.label"
                                   class="text-[10px] font-bold uppercase tracking-widest mb-0.5"
                                   style="color: rgba(255,255,255,0.35); font-family: Manrope, sans-serif;">
                                    {{ unit.label }}
                                </p>
                                <span class="text-sm leading-snug text-white/65"
                                      style="font-family: Newsreader, serif;"
                                      :itemprop="i === 0 ? 'streetAddress' : undefined">
                                    {{ unit.address }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-amber-400/80 text-base shrink-0">call</span>
                            <a :href="`tel:+${phoneRaw}`"
                               class="text-sm text-white/65 hover:text-white transition-colors duration-150"
                               style="font-family: Manrope, sans-serif;"
                               itemprop="telephone">
                                {{ phone }}
                            </a>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-amber-400/80 text-base shrink-0">mail</span>
                            <a :href="`mailto:${email}`"
                               class="text-sm text-white/65 hover:text-white transition-colors duration-150"
                               style="font-family: Manrope, sans-serif;"
                               itemprop="email">
                                {{ email }}
                            </a>
                        </div>
                    </address>

                    <a
                        :href="whatsappUrl"
                        target="_blank" rel="noopener noreferrer"
                        class="mt-6 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 hover:opacity-90 active:scale-95"
                        style="background-color: #25D366; color: #fff; font-family: Manrope, sans-serif;"
                    >
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        {{ t('Fale pelo WhatsApp', 'Chat on WhatsApp') }}
                    </a>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-8">
                <p class="text-sm text-center sm:text-left text-white/70"
                   style="font-family: Manrope, sans-serif;">
                    &copy; {{ currentYear }} Trataplan &ndash;
                    {{ t('Todos os direitos reservados.', 'All rights reserved.') }}
                </p>
                <p class="text-sm flex items-center gap-1.5 text-white/50"
                   style="font-family: Manrope, sans-serif;">
                    <span class="material-symbols-outlined text-sm leading-none text-amber-400/60">location_on</span>
                    Planalto Alegre – SC, Brasil
                </p>
            </div>
        </div>
    </footer>
</template>
