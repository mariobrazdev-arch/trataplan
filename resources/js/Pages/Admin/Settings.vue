<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch, onUnmounted } from 'vue';

defineOptions({ layout: null });

const props = defineProps({ settings: Object });

// Dynamic company units
const defaultUnit = { tipo: 'matriz', label: '', address: '', phone: '', email: '', whatsapp: '', telegram: '', maps_link: '' };
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
    footer_text_pt:     props.settings?.footer_text_pt || '',
    logo_transparent:   null,
    logo_white_bg:      null,
    // SMTP
    mail_from_address:  props.settings?.mail_from_address || '',
    mail_from_name:     props.settings?.mail_from_name    || '',
    mail_host:          props.settings?.mail_host         || '',
    mail_port:          props.settings?.mail_port         || '587',
    mail_username:      props.settings?.mail_username     || '',
    mail_password:      props.settings?.mail_password     || '',
    mail_encryption:    props.settings?.mail_encryption   || 'tls',
});

const previewTransparent = ref(null);
const previewWhiteBg = ref(null);

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

const currentLogoTransparentUrl = computed(() =>
    previewTransparent.value ?? props.settings?.logo_transparent_url ?? null
);
const currentLogoWhiteBgUrl = computed(() =>
    previewWhiteBg.value ?? props.settings?.logo_white_bg_url ?? null
);

function submit() {
    clearTimeout(autoSaveTimer);
    form.company_units = JSON.stringify(units.value);
    form.transform(d => {
        const data = { ...d, _method: 'PATCH' };
        if (!d.logo_transparent) delete data.logo_transparent;
        if (!d.logo_white_bg) delete data.logo_white_bg;
        return data;
    }).post('/admin/configuracoes', { forceFormData: true });
}

// ── Auto-save (campos de texto, sem arquivos) ─────────────────────────────────
const autoSaveStatus = ref('idle'); // 'idle' | 'saving' | 'saved'
let autoSaveTimer = null;

function scheduleAutoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => {
        form.company_units = JSON.stringify(units.value);
        form.transform(d => {
            const data = { ...d, _method: 'PATCH' };
            delete data.logo_transparent;
            delete data.logo_white_bg;
            return data;
        }).post('/admin/configuracoes', {
            forceFormData: true,
            preserveScroll: true,
            onStart:   () => { autoSaveStatus.value = 'saving'; },
            onSuccess: () => {
                autoSaveStatus.value = 'saved';
                setTimeout(() => { autoSaveStatus.value = 'idle'; }, 2500);
            },
            onError: () => { autoSaveStatus.value = 'idle'; },
        });
    }, 1500);
}

watch(
    [
        () => form.company_tagline_pt,
        () => form.footer_text_pt,
        () => form.facebook_url,       () => form.instagram_url, () => form.linkedin_url,
        () => form.mail_from_address,  () => form.mail_from_name,
        () => form.mail_host,          () => form.mail_port,
        () => form.mail_username,      () => form.mail_password,  () => form.mail_encryption,
    ],
    () => scheduleAutoSave()
);
watch(units, () => scheduleAutoSave(), { deep: true });

onUnmounted(() => clearTimeout(autoSaveTimer));
</script>

<template>
    <AdminLayout>
        <template #title>Configurações</template>
        <template #subtitle>Parâmetros gerais e dados institucionais do site.</template>
        <template #actions>
            <transition name="autosave-fade">
                <span v-if="autoSaveStatus === 'saving'" class="flex items-center gap-1.5 text-xs font-label" style="color: #717973;">
                    <span class="material-symbols-outlined text-sm animate-spin">autorenew</span>
                    Salvando...
                </span>
                <span v-else-if="autoSaveStatus === 'saved'" class="flex items-center gap-1.5 text-xs font-label" style="color: #16a34a;">
                    <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">check_circle</span>
                    Salvo automaticamente
                </span>
            </transition>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 px-6 pb-6">
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
                                <div class="flex items-center rounded-md overflow-hidden" style="background-color: #f6f3ee;">
                                    <span class="px-2.5 py-2.5 text-xs font-label shrink-0" style="color: #717973; border-right: 1px solid #c1c8c2;">WA</span>
                                    <input v-model="unit.whatsapp" type="text" placeholder="554911999990000"
                                        class="flex-1 px-2.5 py-2.5 bg-transparent outline-none font-body text-sm"
                                        style="color: #1c1c19; border: none;" />
                                </div>
                                <p v-if="unit.tipo !== 'matriz'" class="font-label text-[9px] italic" style="color: #c1c8c2;">
                                    Os botões do site usam o WhatsApp da Matriz, mas você pode registrar o da filial aqui.
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

                            <!-- Link do Google Maps (embed) -->
                            <div class="space-y-1">
                                <p class="font-label text-[9px] uppercase tracking-widest" style="color: #717973;">Link do Google Maps (embed)</p>
                                <div class="flex items-center rounded-md overflow-hidden" style="background-color: #f6f3ee;">
                                    <span class="px-2.5 py-2.5 shrink-0" style="border-right: 1px solid #c1c8c2;">
                                        <span class="material-symbols-outlined text-sm" style="color: #717973;">map</span>
                                    </span>
                                    <input v-model="unit.maps_link" type="url"
                                        placeholder="https://www.google.com/maps/embed?pb=..."
                                        class="flex-1 px-2.5 py-2.5 bg-transparent outline-none font-body text-sm"
                                        style="color: #1c1c19; border: none;" />
                                </div>
                                <p class="font-label text-[9px] italic" style="color: #c1c8c2;">
                                    No Google Maps: Compartilhar → Incorporar um mapa → copie apenas a URL do atributo src do iframe.
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
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">Tagline Principal (Home)</p>
                        <input v-model="form.company_tagline_pt" type="text"
                            class="w-full rounded-md p-3 font-body text-sm outline-none"
                            style="background-color: #ebe8e3; border: none;" />
                    </div>
                    <div class="space-y-1.5">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">Texto do Rodapé</p>
                        <textarea v-model="form.footer_text_pt" rows="3"
                            class="w-full rounded-md p-3 font-body italic text-sm outline-none resize-none"
                            style="background-color: #ebe8e3; border: none;"></textarea>
                    </div>
                </div>
            </section>

            <!-- ══ 5. CONFIGURAÇÃO DE E-MAIL (SMTP) ══════════════════════ -->
            <section class="rounded-2xl p-6 space-y-5" style="background-color: #f6f3ee;">
                <div class="flex items-center gap-3 mb-1">
                    <span class="material-symbols-outlined" style="color: #6a5e33; font-variation-settings:'FILL' 1;">mail</span>
                    <p class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">Envio de E-mail (SMTP)</p>
                </div>
                <p class="font-body italic text-xs" style="color: #717973;">
                    Configure aqui o servidor SMTP para envio dos formulários de contato. Deixe em branco para usar as configurações do servidor.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">Remetente – E-mail (From)</p>
                        <input v-model="form.mail_from_address" type="email"
                            placeholder="trataplan@trataplan.com.br"
                            class="w-full rounded-md p-3 font-body text-sm outline-none"
                            style="background-color: #ebe8e3; border: none;" />
                        <p v-if="form.errors.mail_from_address" class="text-xs text-red-500">{{ form.errors.mail_from_address }}</p>
                    </div>
                    <div class="space-y-1.5">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">Remetente – Nome</p>
                        <input v-model="form.mail_from_name" type="text"
                            placeholder="Trataplan"
                            class="w-full rounded-md p-3 font-body text-sm outline-none"
                            style="background-color: #ebe8e3; border: none;" />
                    </div>
                    <div class="space-y-1.5">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">Servidor SMTP (Host)</p>
                        <input v-model="form.mail_host" type="text"
                            placeholder="mail.trataplan.com.br"
                            class="w-full rounded-md p-3 font-body text-sm outline-none"
                            style="background-color: #ebe8e3; border: none;" />
                        <p v-if="form.errors.mail_host" class="text-xs text-red-500">{{ form.errors.mail_host }}</p>
                    </div>
                    <div class="space-y-1.5">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">Porta</p>
                        <div class="flex gap-2">
                            <input v-model="form.mail_port" type="number"
                                placeholder="587"
                                class="w-28 rounded-md p-3 font-body text-sm outline-none"
                                style="background-color: #ebe8e3; border: none;" />
                            <div class="flex gap-1">
                                <button v-for="enc in ['tls','ssl','none']" :key="enc" type="button"
                                    @click="form.mail_encryption = enc"
                                    class="px-3 py-2 rounded-md font-label text-[10px] uppercase tracking-widest font-bold transition-all"
                                    :style="form.mail_encryption === enc
                                        ? 'background-color: #012d1d; color: white;'
                                        : 'background-color: #ebe8e3; color: #717973;'">
                                    {{ enc.toUpperCase() }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">Usuário SMTP</p>
                        <input v-model="form.mail_username" type="text"
                            placeholder="trataplan@trataplan.com.br"
                            class="w-full rounded-md p-3 font-body text-sm outline-none"
                            style="background-color: #ebe8e3; border: none;" />
                    </div>
                    <div class="space-y-1.5">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #77574d;">Senha SMTP</p>
                        <input v-model="form.mail_password" type="password"
                            placeholder="••••••••"
                            autocomplete="new-password"
                            class="w-full rounded-md p-3 font-body text-sm outline-none"
                            style="background-color: #ebe8e3; border: none;" />
                    </div>
                </div>

                <div class="flex items-start gap-2 p-3 rounded-lg" style="background-color: rgba(106,94,51,0.1);">
                    <span class="material-symbols-outlined text-sm shrink-0 mt-0.5" style="color: #6a5e33;">info</span>
                    <p class="font-body text-xs" style="color: #6a5e33;">
                        As mensagens do formulário "Fale Conosco" serão enviadas para o <strong>E-mail de Contato</strong> configurado nas Unidades.
                        Certifique-se de que o usuário SMTP tem permissão para enviar e-mails.
                    </p>
                </div>
            </section>

        </form>
    </AdminLayout>
</template>

<style scoped>
.autosave-fade-enter-active,
.autosave-fade-leave-active {
    transition: opacity 0.3s ease;
}
.autosave-fade-enter-from,
.autosave-fade-leave-to {
    opacity: 0;
}

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
