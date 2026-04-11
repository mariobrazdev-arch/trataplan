<script setup>
import SeoHead from '@/Components/Shared/SeoHead.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useLocale } from '@/composables/useLocale';
import { useSettings } from '@/composables/useSettings';

const props = defineProps({ seo: Object });

const { locale, t } = useLocale();
const { whatsappUrl, telegramLink, setting } = useSettings();
const page   = usePage();
const success = computed(() => page.props.flash?.success);

const address = computed(() => setting('contact_address', 'SC-283, Planalto Alegre - SC, 89882-000'));
const phone   = computed(() => setting('contact_phone',   '+55 49 9960-0329'));
const email   = computed(() => setting('contact_email',   'trataplan@trataplan.com.br'));

const projectTypes = computed(() => [
    { value: '',                      label: t('Selecione um interesse...', 'Select an interest...') },
    { value: 'decks-pergolados',      label: t('Decks & Pergolados',        'Decks & Pergolas') },
    { value: 'construcao-civil',      label: t('Construção Civil',           'Civil Construction') },
    { value: 'cercas-currais',        label: t('Cercas e Currais',           'Fences & Pens') },
    { value: 'paisagismo',            label: t('Paisagismo',                 'Landscaping') },
    { value: 'outro',                 label: t('Outro / Dúvida geral',       'Other / General question') },
]);

const form = useForm({
    name:         '',
    email:        '',
    phone:        '',
    subject:      '',
    message:      '',
});

function submit() {
    const action = locale.value === 'en' ? '/en/contact' : '/contato';
    form.post(action, { onSuccess: () => form.reset() });
}
</script>

<template>
    <SeoHead v-bind="seo" />

    <!-- ─── HERO + FORM ──────────────────────────────────────────────────────── -->
    <section class="pt-28 pb-0" style="background-color: #fcf9f4;">
        <div class="container mx-auto px-6 max-w-7xl">

            <!-- Label topo -->
            <div class="mb-10">
                <span class="inline-block text-xs font-black uppercase tracking-[0.3em]"
                      style="color: #fbbf24; font-family: Manrope, sans-serif; background-color: rgba(251,191,36,0.12); padding: 4px 12px; border-radius: 99px;">
                    {{ t('Fale Conosco', 'Contact Us') }}
                </span>
            </div>

            <div class="grid lg:grid-cols-[1fr_380px] gap-16 items-start pb-24">

                <!-- ── COLUNA ESQUERDA: cabeçalho + formulário ── -->
                <div>
                    <h1 class="font-headline font-extrabold leading-[1.05] tracking-tight mb-4"
                        style="font-size: clamp(2.5rem, 5vw, 4rem); color: #012d1d;">
                        {{ t('Entre em Contato.', 'Get in Touch.') }}
                    </h1>
                    <p class="text-lg mb-10 max-w-lg" style="color: #717973; font-family: Newsreader, serif; font-style: italic;">
                        {{ t(
                            'Da estrutura ao acabamento, estamos prontos para discutir o seu próximo projeto de madeira tratada.',
                            'From structure to finish, we\'re ready to discuss your next treated wood project.'
                        ) }}
                    </p>

                    <!-- Sucesso -->
                    <div v-if="success"
                         class="mb-8 flex items-start gap-4 rounded-2xl p-6"
                         style="background-color: #c1ecd4;">
                        <span class="material-symbols-outlined text-2xl mt-0.5 shrink-0"
                              style="color: #012d1d; font-variation-settings: 'FILL' 1;">check_circle</span>
                        <div>
                            <p class="font-bold text-base" style="color: #012d1d; font-family: Manrope, sans-serif;">
                                {{ t('Mensagem enviada!', 'Message sent!') }}
                            </p>
                            <p class="text-sm mt-1" style="color: #2d5a40; font-family: Newsreader, serif;">
                                {{ t('Retornaremos em breve pelo e-mail informado.', 'We\'ll get back to you soon at the provided email.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Formulário -->
                    <form v-else @submit.prevent="submit" class="space-y-5">

                        <!-- Nome -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest mb-2"
                                   style="font-family: Manrope, sans-serif; color: #9a8b7a;">
                                {{ t('Seu Nome', 'Your Name') }}
                            </label>
                            <input
                                v-model="form.name"
                                type="text" required
                                class="w-full px-5 py-4 rounded-xl outline-none transition-all focus:ring-2"
                                style="background-color: #edeae5; border: none; font-family: Newsreader, serif; font-size: 1rem; color: #1c1c19; --tw-ring-color: #012d1d;"
                                :placeholder="t('Nome completo', 'Full name')"
                            />
                            <p v-if="form.errors.name" class="mt-1.5 text-xs" style="color: #ba1a1a;">{{ form.errors.name }}</p>
                        </div>

                        <!-- Email + Telefone -->
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest mb-2"
                                       style="font-family: Manrope, sans-serif; color: #9a8b7a;">
                                    Email
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email" required
                                    class="w-full px-5 py-4 rounded-xl outline-none transition-all focus:ring-2"
                                    style="background-color: #edeae5; border: none; font-family: Newsreader, serif; font-size: 1rem; color: #1c1c19;"
                                    placeholder="seu@email.com"
                                />
                                <p v-if="form.errors.email" class="mt-1.5 text-xs" style="color: #ba1a1a;">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest mb-2"
                                       style="font-family: Manrope, sans-serif; color: #9a8b7a;">
                                    {{ t('Telefone', 'Phone') }}
                                </label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    class="w-full px-5 py-4 rounded-xl outline-none transition-all focus:ring-2"
                                    style="background-color: #edeae5; border: none; font-family: Newsreader, serif; font-size: 1rem; color: #1c1c19;"
                                    placeholder="+55 49 9960-0329"
                                />
                            </div>
                        </div>

                        <!-- Tipo de projeto -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest mb-2"
                                   style="font-family: Manrope, sans-serif; color: #9a8b7a;">
                                {{ t('Tipo de Projeto', 'Project Type') }}
                            </label>
                            <div class="relative">
                                <select
                                    v-model="form.subject"
                                    class="w-full px-5 py-4 rounded-xl outline-none appearance-none transition-all focus:ring-2 cursor-pointer"
                                    style="background-color: #edeae5; border: none; font-family: Manrope, sans-serif; font-size: 0.95rem; color: #1c1c19;"
                                >
                                    <option v-for="opt in projectTypes" :key="opt.value" :value="opt.value">
                                        {{ opt.label }}
                                    </option>
                                </select>
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-xl"
                                      style="color: #9a8b7a;">expand_more</span>
                            </div>
                        </div>

                        <!-- Mensagem -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest mb-2"
                                   style="font-family: Manrope, sans-serif; color: #9a8b7a;">
                                {{ t('Mensagem', 'Message') }}
                            </label>
                            <textarea
                                v-model="form.message"
                                rows="5" required
                                class="w-full px-5 py-4 rounded-xl outline-none resize-none transition-all focus:ring-2"
                                style="background-color: #edeae5; border: none; font-family: Newsreader, serif; font-size: 1rem; color: #1c1c19;"
                                :placeholder="t('Descreva seu projeto ou dúvida em detalhes...', 'Describe your project or question in detail...')"
                            ></textarea>
                            <p v-if="form.errors.message" class="mt-1.5 text-xs" style="color: #ba1a1a;">{{ form.errors.message }}</p>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="group flex items-center gap-3 px-8 py-4 rounded-xl font-bold text-white transition-all hover:opacity-90 active:scale-95 disabled:opacity-60"
                            style="background-color: #012d1d; font-family: Manrope, sans-serif;"
                        >
                            <span v-if="form.processing"
                                  class="material-symbols-outlined text-base animate-spin">autorenew</span>
                            {{ form.processing ? t('Enviando...', 'Sending...') : t('Enviar Mensagem', 'Send Message') }}
                            <span v-if="!form.processing"
                                  class="material-symbols-outlined text-xl transition-transform group-hover:translate-x-1">
                                arrow_forward
                            </span>
                        </button>
                    </form>
                </div>

                <!-- ── COLUNA DIREITA: info + imagem ── -->
                <div class="space-y-6 lg:pt-24">

                    <!-- Cards de contato -->
                    <div class="rounded-2xl overflow-hidden divide-y" style="background-color: #fff; divide-color: #f0ede8;">

                        <div class="flex items-start gap-4 p-5">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                                 style="background-color: #f0ede8;">
                                <span class="material-symbols-outlined text-base" style="color: #012d1d;">location_on</span>
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest mb-1"
                                   style="color: #9a8b7a; font-family: Manrope, sans-serif;">
                                    {{ t('Endereço', 'Address') }}
                                </p>
                                <p class="text-sm leading-snug" style="color: #1c1c19; font-family: Newsreader, serif;">{{ address }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-5">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                                 style="background-color: #f0ede8;">
                                <span class="material-symbols-outlined text-base" style="color: #012d1d;">call</span>
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest mb-1"
                                   style="color: #9a8b7a; font-family: Manrope, sans-serif;">
                                    {{ t('Telefone', 'Phone') }}
                                </p>
                                <a :href="`tel:${phone.replace(/\D/g,'')}`"
                                   class="text-sm font-semibold hover:underline"
                                   style="color: #012d1d; font-family: Manrope, sans-serif;">
                                    {{ phone }}
                                </a>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-5">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                                 style="background-color: #f0ede8;">
                                <span class="material-symbols-outlined text-base" style="color: #012d1d;">mail</span>
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest mb-1"
                                   style="color: #9a8b7a; font-family: Manrope, sans-serif;">
                                    Email
                                </p>
                                <a :href="`mailto:${email}`"
                                   class="text-sm hover:underline"
                                   style="color: #012d1d; font-family: Manrope, sans-serif;">
                                    {{ email }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Imagem -->
                    <div class="rounded-2xl overflow-hidden relative" style="height: 220px;">
                        <img
                            src="https://images.unsplash.com/photo-1448375240586-882707db888b?w=800&q=80"
                            alt="Trataplan"
                            class="w-full h-full object-cover"
                        />
                        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(1,45,29,0.6) 0%, transparent 60%);"></div>
                        <div class="absolute bottom-4 left-5">
                            <p class="text-white font-black text-lg leading-tight" style="font-family: Manrope, sans-serif;">Trataplan</p>
                            <p class="text-xs" style="color: rgba(255,255,255,0.6); font-family: Manrope, sans-serif;">{{ address }}</p>
                        </div>
                    </div>

                    <!-- Botões rápidos -->
                    <div class="flex gap-3">
                        <a :href="whatsappUrl" target="_blank" rel="noopener"
                           class="flex-1 flex items-center justify-center gap-2 py-3.5 rounded-xl font-bold text-sm text-white transition-all hover:opacity-90"
                           style="background-color: #25D366; font-family: Manrope, sans-serif;">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            WhatsApp
                        </a>
                        <a v-if="telegramLink" :href="telegramLink" target="_blank" rel="noopener"
                           class="flex-1 flex items-center justify-center gap-2 py-3.5 rounded-xl font-bold text-sm text-white transition-all hover:opacity-90"
                           style="background-color: #2AABEE; font-family: Manrope, sans-serif;">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.26 13.955l-2.956-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.884.604z"/>
                            </svg>
                            Telegram
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── VISITE NOSSA SEDE ─────────────────────────────────────────────────── -->
    <section class="py-20" style="background-color: #011a10;">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid md:grid-cols-2 gap-12 items-center">

                <!-- Texto -->
                <div>
                    <span class="text-xs font-black uppercase tracking-[0.3em] mb-4 block"
                          style="color: rgba(251,191,36,0.7); font-family: Manrope, sans-serif;">
                        {{ t('Presença física', 'Physical presence') }}
                    </span>
                    <h2 class="text-white font-headline font-extrabold text-4xl md:text-5xl leading-tight mb-4">
                        {{ t('Visite Nossa Sede.', 'Visit Our HQ.') }}
                    </h2>
                    <p class="text-lg mb-8" style="color: rgba(255,255,255,0.5); font-family: Newsreader, serif; font-style: italic;">
                        {{ address }}
                    </p>

                    <!-- Horário -->
                    <div class="flex items-center gap-4 mb-8 p-5 rounded-2xl" style="background: rgba(255,255,255,0.05);">
                        <span class="material-symbols-outlined text-amber-400 text-2xl shrink-0">schedule</span>
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest mb-1"
                               style="color: rgba(255,255,255,0.35); font-family: Manrope, sans-serif;">
                                {{ t('Horário de Atendimento', 'Business Hours') }}
                            </p>
                            <p class="font-bold" style="color: #fff; font-family: Manrope, sans-serif;">
                                {{ t('Seg – Sex, 08:00 – 18:00', 'Mon – Fri, 08:00 AM – 06:00 PM') }}
                            </p>
                        </div>
                    </div>

                    <!-- Botão Maps -->
                    <a
                        :href="'https://www.google.com/maps/search/' + encodeURIComponent(address)"
                        target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-xl font-bold text-sm transition-all hover:opacity-90 active:scale-95"
                        style="background-color: #fbbf24; color: #012d1d; font-family: Manrope, sans-serif;"
                    >
                        <span class="material-symbols-outlined text-base">open_in_new</span>
                        {{ t('Como Chegar', 'Get Directions') }}
                    </a>
                </div>

                <!-- Mapa -->
                <div class="rounded-2xl overflow-hidden shadow-2xl" style="height: 320px;">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3604!2d-53.08!3d-27.08!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjfCsDA0JzQ4LjAiUyA1M8KwMDQnNDguMCJX!5e0!3m2!1spt-BR!2sbr!4v1600000000000"
                        class="w-full h-full"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        title="Trataplan"
                    ></iframe>
                </div>
            </div>
        </div>
    </section>
</template>
