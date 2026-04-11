<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import PhotoUploader from '@/Components/Admin/PhotoUploader.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineOptions({ layout: null });

const props = defineProps({ post: Object, postCategories: Array });

const isEditing = computed(() => !!props.post);
const activeTab = ref('pt');
const seoOpen = ref(false);
const previewImage = ref(props.post?.featured_image_url || null);

const form = useForm({
    title_pt: props.post?.title_pt || '',
    title_en: props.post?.title_en || '',
    slug: props.post?.slug || '',
    content_pt: props.post?.content_pt || '',
    content_en: props.post?.content_en || '',
    excerpt_pt: props.post?.excerpt_pt || '',
    excerpt_en: props.post?.excerpt_en || '',
    meta_title_pt: props.post?.meta_title_pt || '',
    meta_title_en: props.post?.meta_title_en || '',
    meta_description_pt: props.post?.meta_description_pt || '',
    meta_description_en: props.post?.meta_description_en || '',
    featured_image: null,
    new_photos: [],
    published_at: props.post?.published_at || '',
    post_category_id: props.post?.post_category_id || '',
});

function handleImage(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.featured_image = file;
    previewImage.value = URL.createObjectURL(file);
}

function removeCover() {
    form.featured_image = null;
    previewImage.value = null;
}

function submit() {
    if (isEditing.value) {
        form.transform(data => ({ ...data, _method: 'PATCH' }))
            .post(`/admin/posts/${props.post.id}`, { forceFormData: true });
    } else {
        form.post('/admin/posts', { forceFormData: true });
    }
}

const isPublished = computed(() => !!form.published_at);
</script>

<template>
    <AdminLayout>
        <template #breadcrumb>Admin &rsaquo; Últimos Trabalhos &rsaquo; {{ isEditing ? 'Editar' : 'Nova Postagem' }}</template>
        <template #title>{{ isEditing ? post.title_pt || 'Editar Trabalho' : 'Nova Postagem – Últimos Trabalhos' }}</template>
        <template #actions>
            <div class="flex items-center gap-3">
                <a href="/admin/posts"
                    class="px-5 py-2.5 rounded-lg font-headline font-bold text-sm transition-colors"
                    style="color: #717973; background-color: #ebe8e3;">
                    Cancel
                </a>
                <button type="submit" form="post-form" :disabled="form.processing"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95 disabled:opacity-60"
                    style="background-color: #012d1d;">
                    <span v-if="form.processing" class="material-symbols-outlined text-base animate-spin">autorenew</span>
                    <span v-else class="material-symbols-outlined text-base">publish</span>
                    {{ isEditing ? 'Salvar' : 'Publicar' }}
                </button>
            </div>
        </template>

        <form id="post-form" @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- ── LEFT: Conteúdo ─────────────────────────── -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- Language tabs + content card -->
                    <div class="rounded-xl overflow-hidden" style="background-color: #ffffff;">

                        <!-- Tab header -->
                        <div class="flex items-center gap-1 px-6 pt-5 pb-0">
                            <button type="button" @click="activeTab = 'pt'"
                                class="px-5 py-2.5 rounded-t-lg font-label text-sm font-bold uppercase tracking-wide transition-all border-b-2"
                                :style="activeTab === 'pt'
                                    ? 'color: #012d1d; border-color: #012d1d; background-color: #f6f3ee;'
                                    : 'color: #717973; border-color: transparent;'">
                                Português
                            </button>
                            <button type="button" @click="activeTab = 'en'"
                                class="px-5 py-2.5 rounded-t-lg font-label text-sm font-bold uppercase tracking-wide transition-all border-b-2"
                                :style="activeTab === 'en'
                                    ? 'color: #012d1d; border-color: #012d1d; background-color: #f6f3ee;'
                                    : 'color: #717973; border-color: transparent;'">
                                English
                            </button>
                        </div>

                        <!-- Tab content -->
                        <div class="p-6 space-y-6" style="background-color: #f6f3ee;">

                            <!-- PT tab -->
                            <template v-if="activeTab === 'pt'">
                                <div class="space-y-2">
                                    <p class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">Título do Serviço</p>
                                    <input v-model="form.title_pt" required type="text"
                                        placeholder="Ex: Residência Pinheiros – Estrutura em Madeira"
                                        class="w-full bg-transparent outline-none font-headline font-extrabold text-primary leading-tight transition-all"
                                        style="font-size: 1.6rem; border: none; border-bottom: 2px solid #c1c8c2;"
                                        @focus="$event.target.style.borderColor='#012d1d'"
                                        @blur="$event.target.style.borderColor='#c1c8c2'"
                                    />
                                    <p v-if="form.errors.title_pt" class="text-xs text-red-500">{{ form.errors.title_pt }}</p>
                                </div>

                                <div class="space-y-2">
                                    <p class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">Resumo</p>
                                    <textarea v-model="form.excerpt_pt" rows="3"
                                        placeholder="Uma breve visão geral do projeto, materiais utilizados e desafio arquitetônico..."
                                        class="w-full bg-transparent outline-none font-body italic text-lg leading-relaxed resize-none"
                                        style="border: none; border-bottom: 1px solid #c1c8c2; color: #414844;"
                                    ></textarea>
                                </div>

                                <!-- Formatting toolbar (decorativo) -->
                                <div class="flex items-center gap-1 py-2 border-y" style="border-color: #c1c8c2;">
                                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded font-body font-bold text-sm hover:bg-white/60 transition-colors" style="color: #414844;"><b>B</b></button>
                                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded font-body italic text-sm hover:bg-white/60 transition-colors" style="color: #414844;"><i>I</i></button>
                                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded hover:bg-white/60 transition-colors" style="color: #414844;">
                                        <span class="material-symbols-outlined text-base">format_list_bulleted</span>
                                    </button>
                                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded hover:bg-white/60 transition-colors" style="color: #414844;">
                                        <span class="material-symbols-outlined text-base">link</span>
                                    </button>
                                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded hover:bg-white/60 transition-colors" style="color: #414844;">
                                        <span class="material-symbols-outlined text-base">image</span>
                                    </button>
                                </div>

                                <div class="space-y-2">
                                    <textarea v-model="form.content_pt" rows="14"
                                        placeholder="Descreva o processo construtivo, as tecnologias sustentáveis aplicadas e o impacto visual desta obra..."
                                        class="w-full bg-transparent outline-none font-body text-base leading-relaxed resize-y"
                                        style="border: none; color: #1c1c19; min-height: 300px;"
                                    ></textarea>
                                </div>
                            </template>

                            <!-- EN tab -->
                            <template v-else-if="activeTab === 'en'">
                                <div class="space-y-2">
                                    <p class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">Service Title</p>
                                    <input v-model="form.title_en" type="text"
                                        placeholder="Ex: Pinheiros Residence – Timber Structure"
                                        class="w-full bg-transparent outline-none font-headline font-extrabold text-primary leading-tight transition-all"
                                        style="font-size: 1.6rem; border: none; border-bottom: 2px solid #c1c8c2;"
                                        @focus="$event.target.style.borderColor='#012d1d'"
                                        @blur="$event.target.style.borderColor='#c1c8c2'"
                                    />
                                    <p v-if="form.errors.title_en" class="text-xs text-red-500">{{ form.errors.title_en }}</p>
                                </div>

                                <div class="space-y-2">
                                    <p class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">Brief Summary</p>
                                    <textarea v-model="form.excerpt_en" rows="3"
                                        placeholder="A brief overview of the project, materials used and architectural challenge..."
                                        class="w-full bg-transparent outline-none font-body italic text-lg leading-relaxed resize-none"
                                        style="border: none; border-bottom: 1px solid #c1c8c2; color: #414844;"
                                    ></textarea>
                                </div>

                                <!-- Formatting toolbar (decorativo) -->
                                <div class="flex items-center gap-1 py-2 border-y" style="border-color: #c1c8c2;">
                                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded font-body font-bold text-sm hover:bg-white/60 transition-colors" style="color: #414844;"><b>B</b></button>
                                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded font-body italic text-sm hover:bg-white/60 transition-colors" style="color: #414844;"><i>I</i></button>
                                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded hover:bg-white/60 transition-colors" style="color: #414844;">
                                        <span class="material-symbols-outlined text-base">format_list_bulleted</span>
                                    </button>
                                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded hover:bg-white/60 transition-colors" style="color: #414844;">
                                        <span class="material-symbols-outlined text-base">link</span>
                                    </button>
                                    <button type="button" class="w-8 h-8 flex items-center justify-center rounded hover:bg-white/60 transition-colors" style="color: #414844;">
                                        <span class="material-symbols-outlined text-base">image</span>
                                    </button>
                                </div>

                                <div class="space-y-2">
                                    <textarea v-model="form.content_en" rows="14"
                                        placeholder="Describe the construction process, sustainable technologies applied and the visual impact of this project..."
                                        class="w-full bg-transparent outline-none font-body text-base leading-relaxed resize-y"
                                        style="border: none; color: #1c1c19; min-height: 300px;"
                                    ></textarea>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- SEO Settings accordion -->
                    <div class="rounded-xl overflow-hidden" style="background-color: #ffffff;">
                        <button type="button" @click="seoOpen = !seoOpen"
                            class="w-full flex items-center justify-between px-6 py-4 transition-colors hover:bg-stone-50"
                        >
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-base" style="color: #6a5e33;">manage_search</span>
                                <span class="font-label text-sm font-bold uppercase tracking-[0.15em] text-outline">SEO Settings</span>
                            </div>
                            <span class="material-symbols-outlined text-base transition-transform" :style="seoOpen ? 'transform:rotate(180deg); color:#012d1d;' : 'color:#717973;'">expand_more</span>
                        </button>
                        <Transition
                            enter-active-class="transition-all duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                        >
                            <div v-if="seoOpen" class="px-6 pb-6 pt-2 space-y-5" style="background-color: #f6f3ee;">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-2">
                                        <p class="font-label text-[10px] uppercase tracking-widest text-outline">Meta Título PT <span style="color:#c1c8c2;">(60 chars)</span></p>
                                        <input v-model="form.meta_title_pt" maxlength="60" type="text"
                                            class="w-full rounded-md p-4 font-body text-base outline-none focus:ring-2 focus:ring-primary-fixed-dim"
                                            style="background-color: #ebe8e3; border: none;" />
                                        <p class="text-[10px] font-label" style="color: #717973;">{{ form.meta_title_pt?.length || 0 }}/60</p>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="font-label text-[10px] uppercase tracking-widest text-outline">Meta Title EN <span style="color:#c1c8c2;">(60 chars)</span></p>
                                        <input v-model="form.meta_title_en" maxlength="60" type="text"
                                            class="w-full rounded-md p-4 font-body text-base outline-none focus:ring-2 focus:ring-primary-fixed-dim"
                                            style="background-color: #ebe8e3; border: none;" />
                                        <p class="text-[10px] font-label" style="color: #717973;">{{ form.meta_title_en?.length || 0 }}/60</p>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="font-label text-[10px] uppercase tracking-widest text-outline">Meta Descrição PT <span style="color:#c1c8c2;">(160 chars)</span></p>
                                        <textarea v-model="form.meta_description_pt" maxlength="160" rows="3"
                                            class="w-full rounded-md p-4 font-body text-base outline-none resize-none focus:ring-2 focus:ring-primary-fixed-dim"
                                            style="background-color: #ebe8e3; border: none;"></textarea>
                                        <p class="text-[10px] font-label" style="color: #717973;">{{ form.meta_description_pt?.length || 0 }}/160</p>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="font-label text-[10px] uppercase tracking-widest text-outline">Meta Description EN <span style="color:#c1c8c2;">(160 chars)</span></p>
                                        <textarea v-model="form.meta_description_en" maxlength="160" rows="3"
                                            class="w-full rounded-md p-4 font-body text-base outline-none resize-none focus:ring-2 focus:ring-primary-fixed-dim"
                                            style="background-color: #ebe8e3; border: none;"></textarea>
                                        <p class="text-[10px] font-label" style="color: #717973;">{{ form.meta_description_en?.length || 0 }}/160</p>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>

                <!-- ── RIGHT: Sidebar ─────────────────────────── -->
                <div class="lg:col-span-5 space-y-6">

                    <!-- Publicação -->
                    <div class="rounded-xl p-6 space-y-5" style="background-color: #f6f3ee;">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-base" style="color: #6a5e33; font-variation-settings: 'FILL' 1;">settings</span>
                            <p class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">Publicação</p>
                        </div>

                        <div class="space-y-2">
                            <p class="font-label text-[10px] uppercase tracking-widest text-outline">Categoria</p>
                            <select v-model="form.post_category_id"
                                class="w-full rounded-md px-3 py-3 font-body text-sm outline-none focus:ring-2 focus:ring-primary-fixed-dim"
                                style="background-color: #ebe8e3; border: none;">
                                <option value="">— Sem categoria —</option>
                                <option v-for="cat in postCategories" :key="cat.id" :value="cat.id">
                                    {{ cat.name_pt }}{{ cat.name_en ? ` / ${cat.name_en}` : '' }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <p class="font-label text-[10px] uppercase tracking-widest text-outline">URL Slug</p>
                            <div class="flex items-center gap-0 rounded-md overflow-hidden" style="background-color: #ebe8e3;">
                                <span class="px-3 py-3 text-xs font-label shrink-0" style="color: #717973; border-right: 1px solid #c1c8c2;">trataplan.com.br/</span>
                                <input v-model="form.slug" type="text" placeholder="auto-gerado"
                                    class="flex-1 px-3 py-3 bg-transparent outline-none font-mono text-sm"
                                    style="color: #1c1c19; border: none;" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="font-label text-[10px] uppercase tracking-widest text-outline">Data de Publicação</p>
                            <input v-model="form.published_at" type="datetime-local"
                                class="w-full rounded-md p-3 font-body text-sm outline-none focus:ring-2 focus:ring-primary-fixed-dim"
                                style="background-color: #ebe8e3; border: none;" />
                            <p class="text-[10px] font-label" style="color: #717973;">Deixe vazio para salvar como rascunho.</p>
                        </div>
                    </div>

                    <!-- Capa do Projeto -->
                    <div class="rounded-xl p-6 space-y-3" style="background-color: #f6f3ee;">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">Capa do Projeto</p>
                            <button v-if="previewImage" type="button" @click="removeCover"
                                class="font-label text-[10px] uppercase tracking-widest font-bold hover:underline transition-colors"
                                style="color: #ba1a1a;">
                                Remover
                            </button>
                        </div>

                        <label class="cursor-pointer block group">
                            <div class="relative w-full rounded-lg overflow-hidden"
                                style="height: 200px; background-color: #ebe8e3;">
                                <img v-if="previewImage" :src="previewImage" class="w-full h-full object-cover" alt="Capa" />
                                <div v-else class="w-full h-full flex flex-col items-center justify-center gap-3">
                                    <span class="material-symbols-outlined text-5xl" style="color: #c1c8c2;">add_a_photo</span>
                                    <div class="text-center">
                                        <p class="font-body italic text-sm" style="color: #c1c8c2;">Upload Cover</p>
                                        <p class="font-label text-[9px] uppercase tracking-widest mt-1" style="color: #c1c8c2;">Recomendado: 1920×1080px / Máx 5MB</p>
                                    </div>
                                </div>
                                <div v-if="previewImage"
                                    class="absolute inset-0 flex flex-col items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                                    style="background-color: rgba(1,45,29,0.55);">
                                    <span class="material-symbols-outlined text-white text-4xl">add_a_photo</span>
                                    <p class="font-label text-[10px] uppercase tracking-widest text-white">Trocar foto</p>
                                </div>
                            </div>
                            <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleImage" />
                        </label>
                        <p class="font-label text-[9px] uppercase tracking-widest" style="color: #c1c8c2;">Recomendado: 1920×1080px / Máx 5MB</p>
                        <p v-if="form.errors.featured_image" class="text-xs text-red-500">{{ form.errors.featured_image }}</p>
                    </div>

                    <!-- Galeria de Mídia -->
                    <div class="rounded-xl p-6 space-y-3" style="background-color: #f6f3ee;">
                        <p class="font-label text-[10px] uppercase tracking-[0.2em] text-outline mb-4">Galeria de Mídia</p>
                        <PhotoUploader
                            v-model="form.new_photos"
                            :existing="post?.photos || []"
                            delete-url-prefix="/admin/posts/fotos"
                        />
                        <p v-if="form.errors['new_photos.*']" class="text-xs text-red-500">{{ form.errors['new_photos.*'] }}</p>
                    </div>

                    <!-- Status badge -->
                    <div class="rounded-xl p-5 flex items-center gap-4" style="background-color: #fed3c7;">
                        <span class="material-symbols-outlined text-2xl flex-shrink-0" style="color: #77574d; font-variation-settings: 'FILL' 1;">verified</span>
                        <div>
                            <p class="font-headline font-bold text-sm" style="color: #012d1d;">
                                {{ isPublished ? 'Agendado para Publicação' : 'Rascunho' }}
                            </p>
                            <p class="font-body italic text-[11px] mt-0.5" style="color: #5d4037;">
                                {{ isPublished ? `Publicar em: ${form.published_at}` : 'Preencha a data para publicar automaticamente.' }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </AdminLayout>
</template>
