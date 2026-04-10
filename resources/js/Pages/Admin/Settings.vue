<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineOptions({ layout: null });

const props = defineProps({ settings: Object });

// Dynamic company units
const defaultUnit = { tipo: 'matriz', label: '', address: '', phone: '', email: '', whatsapp: '', telegram: '' };
const units = ref(
    Array.isArray(props.settings?.company_units_decoded) && props.settings.company_units_decoded.length
        ? props.settings.company_units_decoded.map(u => ({ ...defaultUnit, ...u }))
        : [{ ...defaultUnit, tipo: 'matriz', label: 'Matriz (Sede)' }]
);

function addUnit() {
    units.value.push({ ...defaultUnit, tipo: 'filial' });
}
function removeUnit(i) {
    if (units.value.length === 1) return;
    units.value.splice(i, 1);
}

const form = useForm({
    company_units:      '',
    // Keep legacy fields for backward compat — auto-synced from matriz on save
    whatsapp_number:    props.settings?.whatsapp_number || '',
    contact_email:      props.settings?.contact_email || '',
    contact_phone:      props.settings?.contact_phone || '',
    facebook_url:       props.settings?.facebook_url || '',
    instagram_url:      props.settings?.instagram_url || '',
    linkedin_url:       props.settings?.linkedin_url || '',
    company_tagline_pt: props.settings?.company_tagline_pt || '',
    company_tagline_en: props.settings?.company_tagline_en || '',
    footer_text_pt:     props.settings?.footer_text_pt || '',
    footer_text_en:     props.settings?.footer_text_en || '',
    banner_title_pt:    props.settings?.banner_title_pt || '',
    banner_title_en:    props.settings?.banner_title_en || '',
    banner_subtitle_pt: props.settings?.banner_subtitle_pt || '',
    banner_subtitle_en: props.settings?.banner_subtitle_en || '',
    banner_image:       null,
    logo_transparent:   null,
    logo_white_bg:      null,
});

const previewTransparent = ref(null);
const previewWhiteBg = ref(null);
const previewBannerImage = ref(null);

function onLogoTransparent(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.logo_transparent = file;
    previewTransparent.value = URL.createObjectURL(file);
}
function onLogoWhiteBg(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.logo_white_bg = file;
    previewWhiteBg.value = URL.createObjectURL(file);
}
function onBannerImage(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.banner_image = file;
    previewBannerImage.value = URL.createObjectURL(file);
}

const currentLogoTransparentUrl = computed(() =>
    previewTransparent.value ?? props.settings?.logo_transparent_url ?? null
);
const currentLogoWhiteBgUrl = computed(() =>
    previewWhiteBg.value ?? props.settings?.logo_white_bg_url ?? null
);
const currentBannerImageUrl = computed(() =>
    previewBannerImage.value ?? props.settings?.banner_image_url ?? null
);

function submit() {
    form.company_units = JSON.stringify(units.value);
    form.transform(d => {
        const data = { ...d, _method: 'PATCH' };
        if (!d.logo_transparent) delete data.logo_transparent;
        if (!d.logo_white_bg) delete data.logo_white_bg;
        if (!d.banner_image) delete data.banner_image;
        return data;
    }).post('/admin/configuracoes', { forceFormData: true });
}
</script>

<template>
    <AdminLayout>
        <template #title>Configurações</template>
        <template #subtitle>Parâmetros gerais e dados institucionais do site.</template>
        <template #actions>
            <button type="submit" form="settings-form" :disabled="form.processing"
                class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95 disabled:opacity-60"
                style="background-color: #012d1d;">
                <span v-if="form.processing" class="material-symbols-outlined text-base animate-spin">autorenew</span>
                <span v-else class="material-symbols-outlined text-base">save</span>
                Salvar Configurações
            </button>
        </template>

        <!-- Flash -->
        <div v-if="$page.props.flash?.success"
            class="flex items-center gap-3 px-5 py-3 rounded-xl mb-6 font-headline font-bold text-sm"
            style="background-color: #d1fae5; color: #012d1d;">
            <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">check_circle</span>
            {{ $page.props.flash.success }}
        </div>

        <form id="settings-form" @submit.prevent="submit" class="space-y-8">

            <!-- ══ 1. IDENTIDADE VISUAL ══════════════════════════════════ -->
            <section class="rounded-2xl overflow-hidden" style="background-color: #f6f3ee;">
                <div class="flex items-center gap-3 px-6 pt-6 pb-4">
                    <span class="material-symbols-outlined" style="color: #6a5e33; font-variation-settings:'FILL' 1;">palette</span>
                    <p class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">Identidade Visual</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                    <!-- Logos -->
                    <div class="px-6 pb-6 space-y-5">
                        <!-- Logo transparente -->
                        <div>
                            <p class="font-label text-[10px] uppercase tracking-widest mb-1" style="color: #77574d;">Logotipo Principal (Light)</p>
                            <p class="font-body italic text-xs mb-3" style="color: #717973;">PNG, SVG — para o header escuro / fundos escuros.</p>
                            <label class="flex flex-col items-center justify-center rounded-xl cursor-pointer transition-all group"
                                style="background-color: #1c1c19; border: 2px dashed #444; min-height: 120px; padding: 1rem;">
                                <img v-if="currentLogoTransparentUrl" :src="currentLogoTransparentUrl"
                                    alt="Logo" class="max-h-20 object-contain mb-2 group-hover:opacity-80 transition-opacity" />
                                <span v-else class="material-symbols-outlined text-3xl mb-1" style="color: #555;">upload</span>
                                <span class="text-xs font-label mt-1" style="color: #666;">
                                    {{ form.logo_transparent ? form.logo_transparent.name : (currentLogoTransparentUrl ? 'Clique para trocar' : 'Clique para enviar') }}
                                </span>
                                <input type="file" class="hidden" accept="image/png,image/svg+xml,image/webp" @change="onLogoTransparent" />
                            </label>
                            <p v-if="form.errors.logo_transparent" class="text-xs mt-1 text-red-500">{{ form.errors.logo_transparent }}</p>
                        </div>

                        <!-- Logo fundo branco -->
                        <div>
                            <p class="font-label text-[10px] uppercase tracking-widest mb-1" style="color: #77574d;">Logotipo Secundário (Dark)</p>
                            <p class="font-body italic text-xs mb-3" style="color: #717973;">PNG, JPG — para o footer e fundos claros.</p>
                            <label class="flex flex-col items-center justify-center rounded-xl cursor-pointer transition-all group"
                                style="background-color: #ebe8e3; border: 2px dashed #c1c8c2; min-height: 120px; padding: 1rem;">
                                <img v-if="currentLogoWhiteBgUrl" :src="currentLogoWhiteBgUrl"
                                    alt="Logo" class="max-h-20 object-contain mb-2 group-hover:opacity-70 transition-opacity" />
                                <span v-else class="material-symbols-outlined text-3xl mb-1" style="color: #aaa;">upload</span>
                                <span class="text-xs font-label mt-1" style="color: #aaa;">
                                    {{ form.logo_white_bg ? form.logo_white_bg.name : (currentLogoWhiteBgUrl ? 'Clique para trocar' : 'Clique para enviar') }}
                                </span>
                                <input type="file" class="hidden" accept="image/png,image/jpeg,image/webp" @change="onLogoWhiteBg" />
                            </label>
                            <p v-if="form.errors.logo_white_bg" class="text-xs mt-1 text-red-500">{{ form.errors.logo_white_bg }}</p>
                        </div>
                    </div>

                    <!-- Banner -->
                    <div class="px-6 pb-6 space-y-5" style="border-left: 1px solid #e0ddd8;">
                        <div>
                            <p class="font-label text-[10px] uppercase tracking-widest mb-1" style="color: #77574d;">Banner Principal (Hero)</p>
                            <p class="font-body italic text-xs mb-3" style="color: #717973;">PNG, JPG ou WebP — recomendado 1920×800px. Máx. 5MB.</p>
                            <label class="relative flex flex-col items-center justify-center rounded-xl cursor-pointer overflow-hidden group"
                                style="background-color: #1c1c19; border: 2px dashed #444; min-height: 140px;">
                                <img v-if="currentBannerImageUrl" :src="currentBannerImageUrl"
                                    alt="Banner" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-40 transition-opacity" />
                                <div class="relative z-10 flex flex-col items-center gap-1">
                                    <span class="material-symbols-outlined text-3xl" style="color: #fff;">photo_library</span>
                                    <span class="text-xs font-label" style="color: #ddd;">
                                        {{ form.banner_image ? form.banner_image.name : (currentBannerImageUrl ? 'Clique para substituir' : 'Clique para enviar') }}
                                    </span>
                                </div>
                                <input type="file" class="hidden" accept="image/png,image/jpeg,image/webp" @change="onBannerImage" />
                            </label>
                            <p v-if="form.errors.banner_image" class="text-xs mt-1 text-red-500">{{ form.errors.banner_image }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="font-label text-[10px] uppercase tracking-widest mb-1.5" style="color: #77574d;">Título PT</p>
                                <input v-model="form.banner_title_pt" type="text" class="w-full rounded-md p-3 font-body text-sm outline-none"
                                    style="background-color: #ebe8e3; border: none;" />
                            </div>
                            <div>
                                <p class="font-label text-[10px] uppercase tracking-widest mb-1.5" style="color: #77574d;">Title EN</p>
                                <input v-model="form.banner_title_en" type="text" class="w-full rounded-md p-3 font-body text-sm outline-none"
                                    style="background-color: #ebe8e3; border: none;" />
                            </div>
                            <div>
                                <p class="font-label text-[10px] uppercase tracking-widest mb-1.5" style="color: #77574d;">Subtítulo PT</p>
                                <textarea v-model="form.banner_subtitle_pt" rows="2"
                                    class="w-full rounded-md p-3 font-body italic text-sm outline-none resize-none"
                                    style="background-color: #ebe8e3; border: none;"></textarea>
                            </div>
                            <div>
                                <p class="font-label text-[10px] uppercase tracking-widest mb-1.5" style="color: #77574d;">Subtitle EN</p>
                                <textarea v-model="form.banner_subtitle_en" rows="2"
                                    class="w-full rounded-md p-3 font-body italic text-sm outline-none resize-none"
                                    style="background-color: #ebe8e3; border: none;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ══ 2. UNIDADES ════════════════════════════════════════ -->
            <section class="rounded-2xl p-6 space-y-4" style="background-color: #f6f3ee;">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined" style="color: #6a5e33; font-variation-settings:'FILL' 1;">location_on</span>
                            <p class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">Unidades Trataplan</p>
                        </div>
                        <button type="button" @click="addUnit"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-lg font-label text-[10px] font-bold uppercase tracking-widest transition-all hover:opacity-80"
                            style="background-color: #012d1d; color: white;">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Nova Unidade
                        </button>
                    </div>

                    <!-- Empty state -->
                    <div v-if="units.length === 0"
                        class="flex flex-col items-center justify-center py-8 rounded-xl"
                        style="background-color: #ebe8e3;">
                        <span class="material-symbols-outlined text-3xl mb-2" style="color: #c1c8c2;">location_off</span>
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #c1c8c2;">Nenhuma unidade cadastrada</p>
                    </div>

                    <!-- Unit cards -->
                    <TransitionGroup name="unit" tag="div" class="space-y-4">
                        <div v-for="(unit, i) in units" :key="i"
                            class="rounded-xl p-5 space-y-4 group"
                            style="background-color: #ebe8e3;">

                            <!-- Header da unidade -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <!-- Tipo selector -->
                                    <button type="button" @click="unit.tipo = 'matriz'"
                                        class="px-3 py-1 rounded-full font-label text-[9px] uppercase tracking-widest font-bold transition-all"
                                        :style="unit.tipo === 'matriz'
                                            ? 'background-color: #012d1d; color: white;'
                                            : 'background-color: #f6f3ee; color: #717973;'">
                                        Matriz
                                    </button>
                                    <button type="button" @click="unit.tipo = 'filial'"
                                        class="px-3 py-1 rounded-full font-label text-[9px] uppercase tracking-widest font-bold transition-all"
                                        :style="unit.tipo === 'filial'
                                            ? 'background-color: #77574d; color: white;'
                                            : 'background-color: #f6f3ee; color: #717973;'">
                                        Filial
                                    </button>
                                    <span v-if="unit.tipo === 'matriz'"
                                        class="font-label text-[9px] italic"
                                        style="color: #6a5e33;">WhatsApp e Telegram do site usarão esta unidade</span>
                                </div>
                                <button type="button" @click="removeUnit(i)"
                                    class="opacity-0 group-hover:opacity-100 transition-opacity font-label text-[9px] uppercase tracking-widest font-bold flex items-center gap-1 hover:underline"
                                    style="color: #ba1a1a;"
                                    :disabled="units.length === 1">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                    Remover
                                </button>
                            </div>

                            <!-- Nome + Endereço -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <p class="font-label text-[9px] uppercase tracking-widest" style="color: #717973;">Nome / Referência</p>
                                    <input v-model="unit.label" type="text"
                                        :placeholder="unit.tipo === 'matriz' ? 'Ex: Matriz (Sede)' : 'Ex: Filial Sul'"
                                        class="w-full rounded-md px-3 py-2.5 font-label text-xs font-bold uppercase tracking-wide outline-none"
                                        style="background-color: #f6f3ee; border: none; color: #1c1c19;" />
                                </div>
                                <div class="space-y-1">
                                    <p class="font-label text-[9px] uppercase tracking-widest" style="color: #717973;">Endereço Completo</p>
                                    <textarea v-model="unit.address" rows="1"
                                        placeholder="Av. Industrial, 4500 – São José dos Campos – SP"
                                        class="w-full rounded-md px-3 py-2.5 font-body text-sm outline-none resize-none"
                                        style="background-color: #f6f3ee; border: none; color: #1c1c19;"></textarea>
                                </div>
                            </div>

                            <!-- Telefone + Email -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <p class="font-label text-[9px] uppercase tracking-widest" style="color: #717973;">Telefone</p>
                                    <div class="flex items-center rounded-md overflow-hidden" style="background-color: #f6f3ee;">
                                        <span class="px-2.5 py-2.5 shrink-0" style="border-right: 1px solid #c1c8c2;">
                                            <span class="material-symbols-outlined text-sm" style="color: #717973;">call</span>
                                        </span>
                                        <input v-model="unit.phone" type="text" placeholder="+55 (11) 4004-1234"
                                            class="flex-1 px-2.5 py-2.5 bg-transparent outline-none font-body text-sm"
                                            style="color: #1c1c19; border: none;" />
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <p class="font-label text-[9px] uppercase tracking-widest" style="color: #717973;">E-mail</p>
                                    <div class="flex items-center rounded-md overflow-hidden" style="background-color: #f6f3ee;">
                                        <span class="px-2.5 py-2.5 shrink-0" style="border-right: 1px solid #c1c8c2;">
                                            <span class="material-symbols-outlined text-sm" style="color: #717973;">mail</span>
                                        </span>
                                        <input v-model="unit.email" type="email" placeholder="contato@trataplan.com.br"
                                            class="flex-1 px-2.5 py-2.5 bg-transparent outline-none font-body text-sm"
                                            style="color: #1c1c19; border: none;" />
                                    </div>
                                </div>
                            </div>

                            <!-- WhatsApp (apenas exibe nota para não-matriz) -->
                            <div class="space-y-1">
                                <p class="font-label text-[9px] uppercase tracking-widest" style="color: #717973;">WhatsApp Business</p>
                                <div class="flex items-center rounded-md overflow-hidden" :style="unit.tipo !== 'matriz' ? 'background-color: #f6f3ee; opacity: 0.6;' : 'background-color: #f6f3ee;'">
                                    <span class="px-2.5 py-2.5 text-xs font-label shrink-0" style="color: #717973; border-right: 1px solid #c1c8c2;">WA</span>
                                    <input v-model="unit.whatsapp" type="text" placeholder="554911999990000"
                                        :readonly="unit.tipo !== 'matriz'"
                                        class="flex-1 px-2.5 py-2.5 bg-transparent outline-none font-body text-sm"
                                        style="color: #1c1c19; border: none;" />
                                </div>
                                <p v-if="unit.tipo !== 'matriz'" class="font-label text-[9px] italic" style="color: #c1c8c2;">
                                    Somente a Matriz tem WhatsApp no site — informe o da Matriz acima.
                                </p>
                                <p v-else class="font-label text-[9px] uppercase tracking-widest" style="color: #c1c8c2;">Apenas números, sem + ou espaços</p>
                            </div>

                            <!-- Telegram da unidade -->
                            <div class="space-y-1">
                                <p class="font-label text-[9px] uppercase tracking-widest" style="color: #717973;">Telegram</p>
                                <div class="flex items-center rounded-md overflow-hidden" style="background-color: #f6f3ee;">
                                    <span class="px-2.5 py-2.5 text-xs font-label shrink-0" style="color: #717973; border-right: 1px solid #c1c8c2;">TG</span>
                                    <input v-model="unit.telegram" type="url" placeholder="https://t.me/canal_empresa"
                                        class="flex-1 px-2.5 py-2.5 bg-transparent outline-none font-body text-sm"
                                        style="color: #1c1c19; border: none;" />
                                </div>
                                <p v-if="unit.tipo !== 'matriz'" class="font-label text-[9px] italic" style="color: #c1c8c2;">
                                    O Telegram dos botões do site usa o da Matriz.
                                </p>
                            </div>
                        </div>
                    </TransitionGroup>

                    <p class="font-label text-[9px] uppercase tracking-widest" style="color: #c1c8c2;">
                        A unidade marcada como <strong>Matriz</strong> define o WhatsApp, Telegram, telefone e e-mail exibidos nos botões e cabeçalho do site.
                    </p>
            </section>

            <!-- ══ 3. REDES SOCIAIS ══════════════════════════════════════ -->
            <section class="rounded-2xl p-6 space-y-5" style="background-color: #f6f3ee;">
                <div class="flex items-center gap-3 mb-1">
                    <span class="material-symbols-outlined" style="color: #6a5e33; font-variation-settings:'FILL' 1;">share</span>
                    <p class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">Redes Sociais</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div v-for="(field, label, icon) in [
                        { model: 'instagram_url', label: 'Instagram', icon: 'photo_camera', placeholder: 'https://instagram.com/...' },
                        { model: 'facebook_url', label: 'Facebook', icon: 'thumb_up', placeholder: 'https://facebook.com/...' },
                        { model: 'linkedin_url', label: 'LinkedIn', icon: 'work', placeholder: 'https://linkedin.com/...' },
                    ]" :key="field.model" class="space-y-1">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">{{ field.label }}</p>
                        <div class="flex items-center rounded-md overflow-hidden" style="background-color: #ebe8e3;">
                            <span class="px-3 py-3 shrink-0" style="border-right: 1px solid #c1c8c2;">
                                <span class="material-symbols-outlined text-base" style="color: #717973;">{{ field.icon }}</span>
                            </span>
                            <input v-model="form[field.model]" type="url" :placeholder="field.placeholder"
                                class="flex-1 px-3 py-3 bg-transparent outline-none font-body text-sm"
                                style="color: #1c1c19; border: none;" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- ══ 4. TEXTOS DO SITE & RODAPÉ ════════════════════════════ -->
            <section class="rounded-2xl p-6 space-y-5" style="background-color: #f6f3ee;">
                <div class="flex items-center gap-3 mb-1">
                    <span class="material-symbols-outlined" style="color: #6a5e33; font-variation-settings:'FILL' 1;">text_fields</span>
                    <p class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">Textos do Site & Rodapé</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">Tagline Principal (Home) PT</p>
                        <input v-model="form.company_tagline_pt" type="text"
                            class="w-full rounded-md p-3 font-body text-sm outline-none"
                            style="background-color: #ebe8e3; border: none;" />
                    </div>
                    <div class="space-y-1.5">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">Tagline Principal (Home) EN</p>
                        <input v-model="form.company_tagline_en" type="text"
                            class="w-full rounded-md p-3 font-body text-sm outline-none"
                            style="background-color: #ebe8e3; border: none;" />
                    </div>
                    <div class="space-y-1.5">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">Footer Text (Português)</p>
                        <textarea v-model="form.footer_text_pt" rows="3"
                            class="w-full rounded-md p-3 font-body italic text-sm outline-none resize-none"
                            style="background-color: #ebe8e3; border: none;"></textarea>
                    </div>
                    <div class="space-y-1.5">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">Footer Text (English)</p>
                        <textarea v-model="form.footer_text_en" rows="3"
                            class="w-full rounded-md p-3 font-body italic text-sm outline-none resize-none"
                            style="background-color: #ebe8e3; border: none;"></textarea>
                    </div>
                </div>
            </section>

        </form>
    </AdminLayout>
</template>

<style scoped>
.unit-enter-active,
.unit-leave-active {
    transition: all 0.25s ease;
}
.unit-enter-from,
.unit-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
