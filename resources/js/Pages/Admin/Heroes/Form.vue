<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import PhotoUploader from '@/Components/Admin/PhotoUploader.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref } from 'vue';

defineOptions({ layout: null });

const props = defineProps({ hero: Object, ctaLinkOptions: { type: Array, default: () => ['whatsapp'] } });
const isEditing = computed(() => !!props.hero);

const layouts = [
    { value: 'full_image', label: 'Imagem Cheia', icon: 'panorama', desc: 'Foto de fundo full-width com texto sobreposto.' },
    { value: 'split_screen', label: 'Split-Screen', icon: 'splitscreen', desc: 'Texto de um lado, imagem + mini-grid do outro.' },
    { value: 'photo_grid', label: 'Grid de Fotos', icon: 'grid_view', desc: 'Colagem de fotos ao fundo com texto centralizado.' },
];

const ctaLinkLabels = {
    whatsapp: 'Abrir WhatsApp',
    home: 'Página Inicial',
    about: 'Sobre',
    portfolio: 'Portfólio',
    blog: 'Blog',
    partners: 'Parceiros',
    contact: 'Contato',
};

const form = useForm({
    layout:        props.hero?.layout || 'full_image',
    title_pt:      props.hero?.title_pt || '',
    subtitle_pt:   props.hero?.subtitle_pt || '',
    cta_label_pt:  props.hero?.cta_label_pt || '',
    cta_link:      props.hero?.cta_link || 'whatsapp',
    main_image:    null,
    is_active:     props.hero?.is_active ?? false,
    new_photos:    [],
});

const selectedLayout = computed(() => layouts.find(l => l.value === form.layout));
const photoLimits = { full_image: 0, split_screen: 3, photo_grid: 6 };
const photoLimit = computed(() => photoLimits[form.layout] ?? 0);
const usesMainImage = computed(() => form.layout === 'full_image' || form.layout === 'split_screen');
const usesPhotos = computed(() => photoLimit.value > 0);

const mainImagePreview = ref(null);

function handleMainImageChange(event) {
    const file = event.target.files?.[0] || null;
    form.main_image = file;
    if (mainImagePreview.value) URL.revokeObjectURL(mainImagePreview.value);
    mainImagePreview.value = file ? URL.createObjectURL(file) : null;
}

onBeforeUnmount(() => { if (mainImagePreview.value) URL.revokeObjectURL(mainImagePreview.value); });

function submit() {
    const opts = { forceFormData: true };
    if (isEditing.value) {
        form.transform(d => ({ ...d, _method: 'PATCH' }))
            .post(`/admin/heroes/${props.hero.id}`, opts);
    } else {
        form.post('/admin/heroes', opts);
    }
}

const mainImageSrc = computed(() => mainImagePreview.value || props.hero?.main_image_url || null);
</script>

<template>
    <AdminLayout>
        <template #breadcrumb>Heroes</template>
        <template #title>{{ isEditing ? 'Editar Hero' : 'Novo Hero' }}</template>
        <template #actions>
            <div class="flex items-center gap-3">
                <a href="/admin/heroes"
                    class="px-5 py-2.5 rounded-lg font-headline font-bold text-sm transition-colors"
                    style="color: #717973; background-color: #ebe8e3;">
                    Cancelar
                </a>
                <button type="submit" form="hero-form" :disabled="form.processing"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95 disabled:opacity-60"
                    style="background-color: #012d1d;">
                    <span v-if="form.processing" class="material-symbols-outlined text-base animate-spin">autorenew</span>
                    <span v-else class="material-symbols-outlined text-base">save</span>
                    {{ isEditing ? 'Salvar Alterações' : 'Salvar Hero' }}
                </button>
            </div>
        </template>

        <form id="hero-form" @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- ── LEFT: Dados ─────────────────────────────── -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- Card seleção de layout (só na criação) -->
                    <div v-if="!isEditing" class="rounded-xl p-8" style="background-color: #f6f3ee;">
                        <h3 class="text-xl font-headline font-bold text-primary mb-6 flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary">dashboard_customize</span>
                            Layout do Hero
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label v-for="l in layouts" :key="l.value"
                                class="rounded-lg p-5 cursor-pointer transition-all border-2"
                                :style="form.layout === l.value
                                    ? 'border-color: #012d1d; background-color: #fff;'
                                    : 'border-color: transparent; background-color: #ebe8e3;'">
                                <input type="radio" v-model="form.layout" :value="l.value" class="sr-only" />
                                <span class="material-symbols-outlined text-3xl block mb-2" style="color: #012d1d;">{{ l.icon }}</span>
                                <p class="font-headline font-bold text-sm text-primary mb-1">{{ l.label }}</p>
                                <p class="font-body italic text-xs" style="color: #717973;">{{ l.desc }}</p>
                            </label>
                        </div>
                    </div>
                    <div v-else class="rounded-xl p-5 flex items-center gap-3" style="background-color: #f6f3ee;">
                        <span class="material-symbols-outlined text-2xl" style="color: #012d1d;">{{ selectedLayout?.icon }}</span>
                        <div>
                            <p class="font-headline font-bold text-sm text-primary">{{ selectedLayout?.label }}</p>
                            <p class="font-body italic text-xs" style="color: #717973;">O layout não pode ser alterado após a criação.</p>
                        </div>
                    </div>

                    <!-- Card dados -->
                    <div class="rounded-xl p-8 relative overflow-hidden" style="background-color: #f6f3ee;">
                        <h3 class="text-xl font-headline font-bold text-primary mb-8 flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary">description</span>
                            Textos do Hero
                        </h3>

                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="font-label text-[10px] uppercase tracking-widest text-outline block">Título *</label>
                                <input v-model="form.title_pt" type="text"
                                    placeholder="Ex: Madeira Tratada que Dura Gerações."
                                    class="w-full rounded-md p-4 font-body text-lg outline-none transition-all focus:ring-2 focus:ring-primary-fixed-dim"
                                    style="background-color: #ebe8e3; border: none;" />
                                <p v-if="form.errors.title_pt" class="text-xs text-red-500">{{ form.errors.title_pt }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="font-label text-[10px] uppercase tracking-widest text-outline block">Subtítulo</label>
                                <textarea v-model="form.subtitle_pt" rows="3"
                                    class="w-full rounded-md p-4 font-body text-base leading-relaxed outline-none resize-none transition-all focus:ring-2 focus:ring-primary-fixed-dim"
                                    style="background-color: #ebe8e3; border: none;"></textarea>
                                <p v-if="form.errors.subtitle_pt" class="text-xs text-red-500">{{ form.errors.subtitle_pt }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="font-label text-[10px] uppercase tracking-widest text-outline block">Texto do botão</label>
                                <input v-model="form.cta_label_pt" type="text"
                                    placeholder="Falar no WhatsApp"
                                    class="w-full rounded-md p-4 font-body text-lg outline-none transition-all focus:ring-2 focus:ring-primary-fixed-dim"
                                    style="background-color: #ebe8e3; border: none;" />
                            </div>

                            <div class="space-y-2">
                                <label class="font-label text-[10px] uppercase tracking-widest text-outline block">Destino do botão</label>
                                <select v-model="form.cta_link"
                                    class="w-full rounded-md p-4 font-body text-lg outline-none transition-all focus:ring-2 focus:ring-primary-fixed-dim"
                                    style="background-color: #ebe8e3; border: none;">
                                    <option v-for="opt in ctaLinkOptions" :key="opt" :value="opt">
                                        {{ ctaLinkLabels[opt] || opt }}
                                    </option>
                                </select>
                                <p class="font-body italic text-xs" style="color: #717973;">
                                    Define para onde o botão leva ao ser clicado. "Abrir WhatsApp" inicia uma conversa; as demais opções levam à página escolhida.
                                </p>
                                <p v-if="form.errors.cta_link" class="text-xs text-red-500">{{ form.errors.cta_link }}</p>
                            </div>

                            <label class="flex items-center gap-3 cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" v-model="form.is_active" class="sr-only" />
                                    <div class="w-11 h-6 rounded-full transition-colors"
                                        :style="form.is_active ? 'background-color:#012d1d;' : 'background-color:#c1c8c2;'"></div>
                                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform shadow"
                                        :style="form.is_active ? 'transform:translateX(20px);' : ''"></div>
                                </div>
                                <span class="font-label text-sm font-bold tracking-wide" style="color:#1c1c19;">
                                    {{ form.is_active ? 'Hero Ativo' : 'Hero Inativo' }}
                                </span>
                            </label>
                            <p v-if="form.errors.is_active" class="text-xs text-red-500">{{ form.errors.is_active }}</p>
                        </div>
                    </div>
                </div>

                <!-- ── RIGHT: Imagens ───────────────────────────── -->
                <div class="lg:col-span-5 space-y-6">

                    <div class="rounded-xl p-8" style="background-color: #f6f3ee;">
                        <h3 class="text-xl font-headline font-bold text-primary mb-8 flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary">photo_library</span>
                            Imagens
                        </h3>

                        <!-- Imagem principal (full_image, split_screen) -->
                        <div v-if="usesMainImage" class="space-y-3 mb-6">
                            <p class="font-label text-[10px] uppercase tracking-widest text-outline">
                                {{ form.layout === 'full_image' ? 'Imagem de Fundo' : 'Imagem Principal' }}
                            </p>
                            <label class="cursor-pointer block group">
                                <div class="relative w-full rounded-lg overflow-hidden transition-all"
                                    style="height: 200px; background-color: #ebe8e3;">
                                    <img v-if="mainImageSrc" :src="mainImageSrc" class="w-full h-full object-cover" alt="Imagem principal" />
                                    <div v-else class="w-full h-full flex flex-col items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-5xl" style="color: #c1c8c2;">add_a_photo</span>
                                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #c1c8c2;">Upload</p>
                                    </div>
                                    <div v-if="mainImageSrc"
                                        class="absolute inset-0 flex flex-col items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                                        style="background-color: rgba(1,45,29,0.55);">
                                        <span class="material-symbols-outlined text-white text-4xl">add_a_photo</span>
                                        <p class="font-label text-[10px] uppercase tracking-widest text-white">Trocar foto</p>
                                    </div>
                                </div>
                                <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleMainImageChange" />
                            </label>
                            <p v-if="form.errors.main_image" class="text-xs text-red-500">{{ form.errors.main_image }}</p>
                        </div>

                        <!-- Fotos múltiplas (split_screen, photo_grid) -->
                        <div v-if="usesPhotos" class="space-y-3">
                            <p class="font-label text-[10px] uppercase tracking-widest text-outline">
                                {{ form.layout === 'split_screen' ? `Fotos do mini-grid (até ${photoLimit})` : `Fotos do grid de fundo (até ${photoLimit})` }}
                            </p>
                            <p v-if="form.layout === 'photo_grid'" class="font-body italic text-xs" style="color: #717973;">
                                Se nenhuma foto for enviada, o grid usa automaticamente as capas dos produtos mais recentes do Portfólio.
                            </p>
                            <PhotoUploader
                                v-model="form.new_photos"
                                :existing="hero?.photos || []"
                                delete-url-prefix="/admin/heroes/fotos"
                            />
                            <p v-if="form.errors['new_photos.*'] || form.errors.new_photos" class="text-xs text-red-500">
                                {{ form.errors['new_photos.*'] || form.errors.new_photos }}
                            </p>
                        </div>
                    </div>

                    <div class="rounded-xl p-5 flex items-start gap-4" style="background-color: #fed3c7;">
                        <span class="material-symbols-outlined mt-0.5" style="color: #77574d; font-variation-settings: 'FILL' 1;">verified</span>
                        <div>
                            <p class="font-headline font-bold text-sm" style="color: #012d1d;">Carrossel automático</p>
                            <p class="font-body italic text-sm mt-1" style="color: #5d4037;">Sempre deve haver ao menos 1 hero ativo. Se 2 ou 3 estiverem ativos, a Home exibe um carrossel automático entre eles.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
