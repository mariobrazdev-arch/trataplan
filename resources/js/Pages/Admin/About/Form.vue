<script setup>
import { ref, reactive, watch, onMounted, onUnmounted } from 'vue';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

defineOptions({ layout: null });

const props = defineProps({
    prefill: Object,
    editId:  Number,
});

// ── Abas ────────────────────────────────────────────────────────────────────
const activeTab = ref('hero');
const tabs = [
    { key: 'hero',    label: 'Hero',         icon: 'image' },
    { key: 'history', label: 'Nossa História', icon: 'history_edu' },
    { key: 'quality', label: 'Nossa Qualidade', icon: 'verified' },
    { key: 'cta',     label: 'Seção Final',   icon: 'campaign' },
];

// ── Repeaters (itens dinâmicos) ──────────────────────────────────────────────
const historyItems = ref(
    props.prefill?.history_items?.length
        ? props.prefill.history_items.map(i => ({ ...i }))
        : [
            { value: '+15',  label_pt: 'Anos de mercado',   label_en: 'Years in market' },
            { value: '100%', label_pt: 'Reflorestamento',   label_en: 'Reforestation' },
            { value: 'SC',   label_pt: 'Santa Catarina',    label_en: 'Santa Catarina' },
          ]
);

const qualityItems = ref(
    props.prefill?.quality_items?.length
        ? props.prefill.quality_items.map(i => ({ ...i }))
        : [
            { icon: 'park',                   title_pt: '100% Reflorestamento',  title_en: '100% Reforestation',         desc_pt: 'Trabalhamos exclusivamente com Eucalipto e Pinus oriundos de florestas plantadas e manejadas de forma sustentável.', desc_en: 'We work exclusively with Eucalyptus and Pine from sustainably managed planted forests.' },
            { icon: 'precision_manufacturing', title_pt: 'Autoclave Vácuo-Pressão', title_en: 'Vacuum-Pressure Autoclave', desc_pt: 'O processo de impregnação sob alta pressão garante penetração total do preservativo em cada fibra da madeira.', desc_en: 'The high-pressure impregnation process ensures total preservative penetration into every wood fiber.' },
            { icon: 'verified_user',            title_pt: 'Garantia de 15 Anos',    title_en: '15-Year Guarantee',         desc_pt: 'Confiança respaldada por certificação e pelo compromisso com a qualidade que só a experiência pode oferecer.', desc_en: 'Confidence backed by certification and the commitment to quality that only experience can offer.' },
          ]
);

function addHistoryItem()  { historyItems.value.push({ value: '', label_pt: '', label_en: '' }); }
function removeHistoryItem(i) { historyItems.value.splice(i, 1); }

function addQualityItem()  {
    qualityItems.value.push({ icon: 'star', title_pt: '', title_en: '', desc_pt: '', desc_en: '' });
}
function removeQualityItem(i) { qualityItems.value.splice(i, 1); }

// ── Previews de foto ─────────────────────────────────────────────────────────
const heroPhotoPreview    = ref(props.prefill?.hero_photo_url    || null);
const historyPhotoPreview = ref(props.prefill?.history_photo_url || null);
const ctaPhotoPreview     = ref(props.prefill?.cta_photo_url     || null);

// ── Formulário ───────────────────────────────────────────────────────────────
const form = useForm({
    version_label:        props.prefill?.version_label        ?? '',
    activate_now:         false,

    hero_title_pt:        props.prefill?.hero_title_pt        ?? '',
    hero_title_en:        props.prefill?.hero_title_en        ?? '',
    hero_subtitle_pt:     props.prefill?.hero_subtitle_pt     ?? '',
    hero_subtitle_en:     props.prefill?.hero_subtitle_en     ?? '',
    hero_photo:           null,
    keep_hero_photo:      props.prefill?.hero_photo_path      ?? '',
    hero_badge_value:     props.prefill?.hero_badge_value     ?? '+15',
    hero_badge_label_pt:  props.prefill?.hero_badge_label_pt  ?? 'Anos de mercado',
    hero_badge_label_en:  props.prefill?.hero_badge_label_en  ?? 'Years in market',

    history_title_pt:     props.prefill?.history_title_pt     ?? '',
    history_title_en:     props.prefill?.history_title_en     ?? '',
    history_content_pt:   props.prefill?.history_content_pt   ?? '',
    history_content_en:   props.prefill?.history_content_en   ?? '',
    history_photo:        null,
    keep_history_photo:   props.prefill?.history_photo_path   ?? '',
    history_youtube_url:  props.prefill?.history_youtube_url  ?? '',

    quality_title_pt:     props.prefill?.quality_title_pt     ?? '',
    quality_title_en:     props.prefill?.quality_title_en     ?? '',
    quality_subtitle_pt:  props.prefill?.quality_subtitle_pt  ?? '',
    quality_subtitle_en:  props.prefill?.quality_subtitle_en  ?? '',

    cta_section_name:     props.prefill?.cta_section_name     ?? 'Seção Reflorestamento',
    cta_title_pt:         props.prefill?.cta_title_pt         ?? '',
    cta_title_en:         props.prefill?.cta_title_en         ?? '',
    cta_subtitle_pt:      props.prefill?.cta_subtitle_pt      ?? '',
    cta_subtitle_en:      props.prefill?.cta_subtitle_en      ?? '',
    cta_photo:            null,
    keep_cta_photo:       props.prefill?.cta_photo_path       ?? '',
});

function onHeroPhoto(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.hero_photo = file;
    form.keep_hero_photo = '';
    heroPhotoPreview.value = URL.createObjectURL(file);
}

function onHistoryPhoto(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.history_photo = file;
    form.keep_history_photo = '';
    historyPhotoPreview.value = URL.createObjectURL(file);
}

function onCtaPhoto(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.cta_photo = file;
    form.keep_cta_photo = '';
    ctaPhotoPreview.value = URL.createObjectURL(file);
}

function submit() {
    clearTimeout(draftTimer);
    const transformed = form.transform(data => ({
        ...data,
        history_items: historyItems.value,
        quality_items: qualityItems.value,
        ...(props.editId ? { _method: 'PUT' } : {}),
    }));

    const url = props.editId ? `/admin/sobre/${props.editId}` : '/admin/sobre';
    transformed.post(url, {
        forceFormData: true,
        onSuccess: () => localStorage.removeItem(DRAFT_KEY),
    });
}

// ── Rascunho automático (localStorage) ───────────────────────────────────
const DRAFT_KEY = 'about_form_draft';
const draftStatus = ref('idle'); // 'idle' | 'saving' | 'saved' | 'restored'
let draftTimer = null;

const DRAFT_TEXT_FIELDS = [
    'version_label',
    'hero_title_pt', 'hero_title_en', 'hero_subtitle_pt', 'hero_subtitle_en',
    'keep_hero_photo', 'hero_badge_value', 'hero_badge_label_pt', 'hero_badge_label_en',
    'history_title_pt', 'history_title_en', 'history_content_pt', 'history_content_en',
    'keep_history_photo', 'history_youtube_url',
    'quality_title_pt', 'quality_title_en', 'quality_subtitle_pt', 'quality_subtitle_en',
    'cta_section_name', 'cta_title_pt', 'cta_title_en', 'cta_subtitle_pt', 'cta_subtitle_en',
    'activate_now',
];

function saveDraft() {
    try {
        const data = {};
        DRAFT_TEXT_FIELDS.forEach(k => { data[k] = form[k]; });
        data.history_items = historyItems.value;
        data.quality_items = qualityItems.value;
        localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
        draftStatus.value = 'saved';
        setTimeout(() => { draftStatus.value = 'idle'; }, 2500);
    } catch {}
}

function scheduleDraft() {
    clearTimeout(draftTimer);
    draftTimer = setTimeout(saveDraft, 1500);
}

onMounted(() => {
    if (props.prefill || props.editId) return; // Não restaura se vier de prefill/edição do servidor
    try {
        const raw = localStorage.getItem(DRAFT_KEY);
        if (!raw) return;
        const draft = JSON.parse(raw);
        DRAFT_TEXT_FIELDS.forEach(k => {
            if (draft[k] !== undefined) form[k] = draft[k];
        });
        if (draft.history_items?.length) historyItems.value = draft.history_items;
        if (draft.quality_items?.length) qualityItems.value = draft.quality_items;
        draftStatus.value = 'restored';
        setTimeout(() => { draftStatus.value = 'idle'; }, 3000);
    } catch {}
});

watch(
    [
        () => form.version_label,
        () => form.hero_title_pt, () => form.hero_title_en,
        () => form.hero_subtitle_pt, () => form.hero_subtitle_en,
        () => form.hero_badge_value, () => form.hero_badge_label_pt, () => form.hero_badge_label_en,
        () => form.history_title_pt, () => form.history_title_en,
        () => form.history_content_pt, () => form.history_content_en,
        () => form.history_youtube_url,
        () => form.quality_title_pt, () => form.quality_title_en,
        () => form.quality_subtitle_pt, () => form.quality_subtitle_en,
        () => form.cta_section_name,
        () => form.cta_title_pt, () => form.cta_title_en,
        () => form.cta_subtitle_pt, () => form.cta_subtitle_en,
    ],
    () => scheduleDraft()
);
watch([historyItems, qualityItems], () => scheduleDraft(), { deep: true });

onUnmounted(() => clearTimeout(draftTimer));
</script>

<template>
    <AdminLayout>
        <template #title>{{ editId ? 'Editar Versão – Página Sobre' : 'Nova Versão – Página Sobre' }}</template>
        <template #subtitle>
            {{ editId ? 'Edite os campos abaixo. As alterações serão salvas nesta versão existente.' : 'Preencha os campos abaixo. Ao salvar, uma nova versão será criada no histórico.' }}
        </template>

        <form @submit.prevent="submit" class="space-y-8">

            <!-- ── Cabeçalho do formulário ── -->
            <div class="rounded-2xl p-6 shadow-sm" style="background-color: #fff;">
                <div class="grid md:grid-cols-2 gap-6 items-end">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest mb-2" style="color: #717973; font-family: Manrope, sans-serif;">
                            Nome desta versão <span style="color: #9ca3af;">(memo interno)</span>
                        </label>
                        <input
                            v-model="form.version_label"
                            type="text"
                            placeholder="ex: Janeiro 2025 – atualização hero"
                            class="w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2"
                            style="border-color: #e5e7eb; color: #1f2937; focus:ring-color: #012d1d;"
                        />
                        <p v-if="form.errors.version_label" class="text-red-500 text-xs mt-1">{{ form.errors.version_label }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            @click="form.activate_now = !form.activate_now"
                            class="relative w-12 h-6 rounded-full transition-colors focus:outline-none"
                            :style="form.activate_now ? 'background-color: #012d1d;' : 'background-color: #d1d5db;'"
                        >
                            <span
                                class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform"
                                :class="form.activate_now ? 'translate-x-6' : 'translate-x-0'"
                            ></span>
                        </button>
                        <label class="text-sm font-bold cursor-pointer" style="color: #012d1d; font-family: Manrope, sans-serif;"
                               @click="form.activate_now = !form.activate_now">
                            Ativar imediatamente após salvar
                        </label>
                    </div>
                </div>
            </div>

            <!-- ── Abas de navegação ── -->
            <div class="flex gap-1 rounded-2xl p-1" style="background-color: #f0ede8;">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    type="button"
                    @click="activeTab = tab.key"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold transition-all"
                    :style="activeTab === tab.key
                        ? 'background-color: #012d1d; color: #fff;'
                        : 'color: #717973;'"
                >
                    <span class="material-symbols-outlined text-base">{{ tab.icon }}</span>
                    <span class="hidden md:inline">{{ tab.label }}</span>
                </button>
            </div>

            <!-- ════════════════════════ ABA: HERO ════════════════════════════ -->
            <div v-show="activeTab === 'hero'" class="space-y-6">
                <div class="rounded-2xl p-6 shadow-sm space-y-6" style="background-color: #fff;">
                    <h3 class="font-headline font-bold text-lg" style="color: #012d1d;">Seção Hero</h3>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="field-label">Título (PT)</label>
                            <input v-model="form.hero_title_pt" type="text" class="field-input" placeholder="Trataplan: A Estrutura da Inovação..." />
                            <p v-if="form.errors.hero_title_pt" class="field-error">{{ form.errors.hero_title_pt }}</p>
                        </div>
                        <div>
                            <label class="field-label">Título (EN)</label>
                            <input v-model="form.hero_title_en" type="text" class="field-input" placeholder="Trataplan: The Structure of..." />
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="field-label">Subtítulo (PT)</label>
                            <textarea v-model="form.hero_subtitle_pt" class="field-input" rows="3"
                                      placeholder="Há mais de uma década transformando madeira..."></textarea>
                        </div>
                        <div>
                            <label class="field-label">Subtítulo (EN)</label>
                            <textarea v-model="form.hero_subtitle_en" class="field-input" rows="3"
                                      placeholder="For over a decade transforming wood..."></textarea>
                        </div>
                    </div>

                    <!-- Foto hero -->
                    <div>
                        <label class="field-label">Foto da seção Hero</label>
                        <div class="flex items-start gap-4">
                            <div v-if="heroPhotoPreview" class="relative w-32 h-20 rounded-xl overflow-hidden flex-shrink-0">
                                <img :src="heroPhotoPreview" class="w-full h-full object-cover" alt="Preview" />
                                <button type="button"
                                        @click="heroPhotoPreview = null; form.hero_photo = null; form.keep_hero_photo = '';"
                                        class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center text-xs">
                                    ×
                                </button>
                            </div>
                            <label class="flex items-center gap-2 px-5 py-3 rounded-xl cursor-pointer transition-colors text-sm font-bold"
                                   style="background-color: #012d1d; color: #fff;">
                                <span class="material-symbols-outlined text-base">upload</span>
                                {{ heroPhotoPreview ? 'Trocar foto' : 'Selecionar foto' }}
                                <input type="file" accept="image/*" class="sr-only" @change="onHeroPhoto" />
                            </label>
                        </div>
                        <p class="text-xs mt-2" style="color: #9ca3af;">Deixe em branco para usar a imagem padrão. Recomendado: 900×600px, máx 3MB.</p>
                        <p v-if="form.errors.hero_photo" class="field-error">{{ form.errors.hero_photo }}</p>
                    </div>

                    <!-- Badge amarela -->
                    <div class="rounded-xl p-4" style="background-color: #fefce8; border: 1px solid #fef08a;">
                        <p class="text-xs font-black uppercase tracking-widest mb-3" style="color: #854d0e; font-family: Manrope, sans-serif;">
                            Badge Amarela (flutuante sobre a foto)
                        </p>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label class="field-label">Valor</label>
                                <input v-model="form.hero_badge_value" type="text" class="field-input" placeholder="+15" />
                            </div>
                            <div>
                                <label class="field-label">Rótulo (PT)</label>
                                <input v-model="form.hero_badge_label_pt" type="text" class="field-input" placeholder="Anos de mercado" />
                            </div>
                            <div>
                                <label class="field-label">Rótulo (EN)</label>
                                <input v-model="form.hero_badge_label_en" type="text" class="field-input" placeholder="Years in market" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════ ABA: HISTÓRIA ════════════════════════ -->
            <div v-show="activeTab === 'history'" class="space-y-6">
                <div class="rounded-2xl p-6 shadow-sm space-y-6" style="background-color: #fff;">
                    <h3 class="font-headline font-bold text-lg" style="color: #012d1d;">Seção Nossa História</h3>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="field-label">Título (PT)</label>
                            <input v-model="form.history_title_pt" type="text" class="field-input" placeholder="Raízes profundas no Oeste Catarinense" />
                        </div>
                        <div>
                            <label class="field-label">Título (EN)</label>
                            <input v-model="form.history_title_en" type="text" class="field-input" placeholder="Deep Roots in Western Santa Catarina" />
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="field-label">Descrição (PT)</label>
                            <p class="text-xs mb-1" style="color: #9ca3af;">Use linha em branco para separar parágrafos.</p>
                            <textarea v-model="form.history_content_pt" class="field-input font-mono" rows="6"
                                      placeholder="A Trataplan nasceu&#10;&#10;Segundo parágrafo..."></textarea>
                        </div>
                        <div>
                            <label class="field-label">Descrição (EN)</label>
                            <p class="text-xs mb-1" style="color: #9ca3af;">Use blank line to separate paragraphs.</p>
                            <textarea v-model="form.history_content_en" class="field-input font-mono" rows="6"
                                      placeholder="Trataplan was born&#10;&#10;Second paragraph..."></textarea>
                        </div>
                    </div>

                    <!-- Vídeo YouTube da seção Histórica -->
                    <div class="rounded-xl p-4" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                        <p class="text-xs font-black uppercase tracking-widest mb-3" style="color: #166534; font-family: Manrope, sans-serif;">
                            Vídeo do YouTube (substitui a foto quando preenchido)
                        </p>
                        <div>
                            <label class="field-label">URL do vídeo</label>
                            <input
                                v-model="form.history_youtube_url"
                                type="text"
                                class="field-input"
                                placeholder="https://www.youtube.com/watch?v=... ou https://youtu.be/..."
                            />
                            <p class="text-xs mt-1" style="color: #6b7280;">Cole o link do YouTube. Se preenchido, o vídeo aparece no lugar da foto. Deixe em branco para usar a foto.</p>
                            <p v-if="form.errors.history_youtube_url" class="field-error">{{ form.errors.history_youtube_url }}</p>
                        </div>
                    </div>

                    <!-- Foto história -->
                    <div>
                        <label class="field-label">Foto da seção Histórica <span style="color: #9ca3af;">(usada quando não há vídeo)</span></label>
                        <div class="flex items-start gap-4">
                            <div v-if="historyPhotoPreview" class="relative w-32 h-20 rounded-xl overflow-hidden flex-shrink-0">
                                <img :src="historyPhotoPreview" class="w-full h-full object-cover" alt="Preview" />
                                <button type="button"
                                        @click="historyPhotoPreview = null; form.history_photo = null; form.keep_history_photo = '';"
                                        class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center text-xs">
                                    ×
                                </button>
                            </div>
                            <label class="flex items-center gap-2 px-5 py-3 rounded-xl cursor-pointer transition-colors text-sm font-bold"
                                   style="background-color: #012d1d; color: #fff;">
                                <span class="material-symbols-outlined text-base">upload</span>
                                {{ historyPhotoPreview ? 'Trocar foto' : 'Selecionar foto' }}
                                <input type="file" accept="image/*" class="sr-only" @change="onHistoryPhoto" />
                            </label>
                        </div>
                        <p v-if="form.errors.history_photo" class="field-error">{{ form.errors.history_photo }}</p>
                    </div>

                    <!-- Itens / Stats -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="field-label mb-0">Itens de destaque (estatísticas)</label>
                            <button type="button" @click="addHistoryItem"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold"
                                    style="background-color: #012d1d; color: #fff;">
                                <span class="material-symbols-outlined text-sm">add</span>
                                Adicionar item
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div v-for="(item, i) in historyItems" :key="i"
                                 class="grid grid-cols-[1fr,2fr,2fr,auto] gap-3 items-center p-3 rounded-xl"
                                 style="background-color: #f8f6f2;">
                                <div>
                                    <label class="field-label text-[10px]">Valor</label>
                                    <input v-model="item.value" type="text" class="field-input text-sm" placeholder="+15" />
                                </div>
                                <div>
                                    <label class="field-label text-[10px]">Rótulo PT</label>
                                    <input v-model="item.label_pt" type="text" class="field-input text-sm" placeholder="Anos de mercado" />
                                </div>
                                <div>
                                    <label class="field-label text-[10px]">Rótulo EN</label>
                                    <input v-model="item.label_en" type="text" class="field-input text-sm" placeholder="Years in market" />
                                </div>
                                <button type="button" @click="removeHistoryItem(i)"
                                        class="mt-4 w-8 h-8 rounded-lg flex items-center justify-center transition-colors hover:bg-red-50"
                                        style="color: #ef4444;">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </div>
                            <p v-if="!historyItems.length" class="text-sm text-center py-4" style="color: #9ca3af;">
                                Nenhum item. Clique em "Adicionar item" para inserir estatísticas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════ ABA: QUALIDADE ════════════════════════ -->
            <div v-show="activeTab === 'quality'" class="space-y-6">
                <div class="rounded-2xl p-6 shadow-sm space-y-6" style="background-color: #fff;">
                    <h3 class="font-headline font-bold text-lg" style="color: #012d1d;">Seção Nossa Qualidade</h3>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="field-label">Título (PT)</label>
                            <input v-model="form.quality_title_pt" type="text" class="field-input" placeholder="Processo e Qualidade" />
                        </div>
                        <div>
                            <label class="field-label">Título (EN)</label>
                            <input v-model="form.quality_title_en" type="text" class="field-input" placeholder="Process and Quality" />
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="field-label">Subtítulo (PT)</label>
                            <textarea v-model="form.quality_subtitle_pt" class="field-input" rows="2"
                                      placeholder="Cada etapa do nosso processo..."></textarea>
                        </div>
                        <div>
                            <label class="field-label">Subtítulo (EN)</label>
                            <textarea v-model="form.quality_subtitle_en" class="field-input" rows="2"
                                      placeholder="Every step of our process..."></textarea>
                        </div>
                    </div>

                    <!-- Cards de qualidade -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <label class="field-label mb-0">Cards de qualidade</label>
                                <p class="text-xs mt-0.5" style="color: #9ca3af;">Com mais de 3 cards, setas de carousel são exibidas no site.</p>
                            </div>
                            <button type="button" @click="addQualityItem"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold"
                                    style="background-color: #012d1d; color: #fff;">
                                <span class="material-symbols-outlined text-sm">add</span>
                                Adicionar card
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div v-for="(item, i) in qualityItems" :key="i"
                                 class="rounded-xl p-4 space-y-3"
                                 style="background-color: #f8f6f2; border: 1px solid #e5e7eb;">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-widest" style="color: #6a5e33; font-family: Manrope, sans-serif;">
                                        Card {{ i + 1 }}
                                    </span>
                                    <button type="button" @click="removeQualityItem(i)"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-red-50 transition-colors"
                                            style="color: #ef4444;">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </div>

                                <div class="grid md:grid-cols-5 gap-3">
                                    <div>
                                        <label class="field-label text-[10px]">Ícone Material</label>
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-lg flex-shrink-0" style="color: #012d1d;">
                                                {{ item.icon || 'star' }}
                                            </span>
                                            <input v-model="item.icon" type="text" class="field-input text-sm" placeholder="park" />
                                        </div>
                                        <p class="text-[10px] mt-0.5" style="color: #9ca3af;">
                                            <a href="https://fonts.google.com/icons" target="_blank" style="color: #6a5e33;">Ver ícones →</a>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="field-label text-[10px]">Título PT</label>
                                        <input v-model="item.title_pt" type="text" class="field-input text-sm" placeholder="100% Reflorestamento" />
                                    </div>
                                    <div>
                                        <label class="field-label text-[10px]">Título EN</label>
                                        <input v-model="item.title_en" type="text" class="field-input text-sm" placeholder="100% Reforestation" />
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="field-label text-[10px]">Descrição PT</label>
                                        <textarea v-model="item.desc_pt" class="field-input text-sm" rows="3"></textarea>
                                    </div>
                                    <div>
                                        <label class="field-label text-[10px]">Descrição EN</label>
                                        <textarea v-model="item.desc_en" class="field-input text-sm" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <p v-if="!qualityItems.length" class="text-sm text-center py-4" style="color: #9ca3af;">
                                Nenhum card. Clique em "Adicionar card".
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════ ABA: CTA ════════════════════════════ -->
            <div v-show="activeTab === 'cta'" class="space-y-6">
                <div class="rounded-2xl p-6 shadow-sm space-y-6" style="background-color: #fff;">
                    <h3 class="font-headline font-bold text-lg" style="color: #012d1d;">Seção Final (acima do rodapé)</h3>

                    <div>
                        <label class="field-label">Nome interno da seção</label>
                        <input v-model="form.cta_section_name" type="text" class="field-input" placeholder="ex: Seção Reflorestamento" />
                        <p class="text-xs mt-1" style="color: #9ca3af;">Apenas para identificação interna, não aparece no site.</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="field-label">Título (PT)</label>
                            <input v-model="form.cta_title_pt" type="text" class="field-input" placeholder="Madeira 100% de Reflorestamento." />
                        </div>
                        <div>
                            <label class="field-label">Título (EN)</label>
                            <input v-model="form.cta_title_en" type="text" class="field-input" placeholder="Wood 100% from Reforestation." />
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="field-label">Subtítulo (PT)</label>
                            <textarea v-model="form.cta_subtitle_pt" class="field-input" rows="3"
                                      placeholder="Nosso compromisso com o planeta..."></textarea>
                        </div>
                        <div>
                            <label class="field-label">Subtítulo (EN)</label>
                            <textarea v-model="form.cta_subtitle_en" class="field-input" rows="3"
                                      placeholder="Our commitment to the planet..."></textarea>
                        </div>
                    </div>

                    <!-- Foto circular da CTA -->
                    <div class="rounded-xl p-4" style="background-color: #f0f9ff; border: 1px solid #bae6fd;">
                        <p class="text-xs font-black uppercase tracking-widest mb-3" style="color: #0369a1; font-family: Manrope, sans-serif;">
                            Foto circular (decorativa no lado direito da seção)
                        </p>
                        <div class="flex items-start gap-4">
                            <div v-if="ctaPhotoPreview" class="relative flex-shrink-0">
                                <div class="w-24 h-24 rounded-full overflow-hidden border-4" style="border-color: rgba(251,191,36,0.4);">
                                    <img :src="ctaPhotoPreview" class="w-full h-full object-cover" alt="Preview CTA" />
                                </div>
                                <button type="button"
                                        @click="ctaPhotoPreview = null; form.cta_photo = null; form.keep_cta_photo = '';"
                                        class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center text-xs">
                                    ×
                                </button>
                            </div>
                            <label class="flex items-center gap-2 px-5 py-3 rounded-xl cursor-pointer transition-colors text-sm font-bold"
                                   style="background-color: #0369a1; color: #fff;">
                                <span class="material-symbols-outlined text-base">upload</span>
                                {{ ctaPhotoPreview ? 'Trocar foto' : 'Selecionar foto circular' }}
                                <input type="file" accept="image/*" class="sr-only" @change="onCtaPhoto" />
                            </label>
                        </div>
                        <p class="text-xs mt-2" style="color: #6b7280;">Deixe em branco para usar a imagem padrão. Recomendado: quadrada ou retrato 600×600px, máx 3MB.</p>
                        <p v-if="form.errors.cta_photo" class="field-error">{{ form.errors.cta_photo }}</p>
                    </div>
                </div>
            </div>

            <!-- ── Barra de ação fixa ── -->
            <div class="flex items-center justify-between rounded-2xl p-5 shadow-sm sticky bottom-4"
                 style="background-color: #fff; border: 1px solid #e5e7eb;">
                <Link href="/admin/sobre"
                      class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-colors hover:bg-gray-50"
                      style="border-color: #e5e7eb; color: #374151;">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Cancelar
                </Link>

                <div class="flex items-center gap-4">
                    <transition name="draft-fade">
                        <span v-if="draftStatus === 'saving'" class="flex items-center gap-1.5 text-xs font-label" style="color: #717973;">
                            <span class="material-symbols-outlined text-sm animate-spin">autorenew</span>
                            Salvando rascunho...
                        </span>
                        <span v-else-if="draftStatus === 'saved'" class="flex items-center gap-1.5 text-xs font-label" style="color: #6b7280;">
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">edit_note</span>
                            Rascunho salvo
                        </span>
                        <span v-else-if="draftStatus === 'restored'" class="flex items-center gap-1.5 text-xs font-label" style="color: #d97706;">
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">restore_page</span>
                            Rascunho restaurado
                        </span>
                    </transition>
                    <span v-if="form.activate_now"
                          class="text-xs font-bold px-3 py-1.5 rounded-full"
                          style="background-color: #dcfce7; color: #16a34a;">
                        Será ativada ao salvar
                    </span>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm transition-all active:scale-95 disabled:opacity-50"
                        style="background-color: #012d1d; color: #fff; font-family: Manrope, sans-serif;"
                    >
                        <span class="material-symbols-outlined text-base">
                            {{ form.processing ? 'hourglass_empty' : 'save' }}
                        </span>
                        {{ form.processing ? 'Salvando...' : (editId ? 'Salvar alterações' : 'Salvar versão') }}
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.field-label {
    display: block;
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #717973;
    font-family: Manrope, sans-serif;
    margin-bottom: 0.375rem;
}
.field-input {
    width: 100%;
    border-radius: 0.625rem;
    border: 1px solid #e5e7eb;
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
    color: #1f2937;
    background-color: #fff;
    transition: border-color 0.15s;
    resize: vertical;
}
.field-input:focus {
    outline: none;
    border-color: #012d1d;
    box-shadow: 0 0 0 3px rgba(1, 45, 29, 0.08);
}
.field-error {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}
.draft-fade-enter-active,
.draft-fade-leave-active {
    transition: opacity 0.3s ease;
}
.draft-fade-enter-from,
.draft-fade-leave-to {
    opacity: 0;
}
</style>
