<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import PhotoUploader from '@/Components/Admin/PhotoUploader.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref } from 'vue';

defineOptions({ layout: null });

const props = defineProps({
    category: Object,
    productCategories: { type: Array, default: () => [] },
});
const isEditing = computed(() => !!props.category);

const form = useForm({
    product_category_id: props.category?.product_category_id ?? null,
    name_pt:         props.category?.name_pt || '',
    name_en:         props.category?.name_en || '',
    description_pt:  props.category?.description_pt || '',
    description_en:  props.category?.description_en || '',
    cover_photo:     null,
    is_active:       props.category?.is_active ?? true,
    new_photos:      [],
});

const coverPreview = ref(null);

function handleCoverChange(event) {
    const file = event.target.files?.[0] || null;
    form.cover_photo = file;
    if (coverPreview.value) URL.revokeObjectURL(coverPreview.value);
    coverPreview.value = file ? URL.createObjectURL(file) : null;
}

onBeforeUnmount(() => { if (coverPreview.value) URL.revokeObjectURL(coverPreview.value); });

function submit() {
    if (isEditing.value) {
        form.transform(d => ({ ...d, _method: 'PATCH' }))
            .post(`/admin/portfolio/categorias/${props.category.id}`, { forceFormData: true });
    } else {
        form.post('/admin/portfolio/categorias', { forceFormData: true });
    }
}

const coverSrc = computed(() => coverPreview.value || props.category?.cover_url || props.category?.photos?.[0]?.url || null);
</script>

<template>
    <AdminLayout>
        <template #breadcrumb>Produtos</template>
        <template #title>{{ isEditing ? 'Editar Produto' : 'Cadastro de Produto' }}</template>
        <template #actions>
            <div class="flex items-center gap-3">
                <a href="/admin/portfolio/categorias"
                    class="px-5 py-2.5 rounded-lg font-headline font-bold text-sm transition-colors"
                    style="color: #717973; background-color: #ebe8e3;">
                    Cancelar
                </a>
                <button type="submit" form="category-form" :disabled="form.processing"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95 disabled:opacity-60"
                    style="background-color: #012d1d;">
                    <span v-if="form.processing" class="material-symbols-outlined text-base animate-spin">autorenew</span>
                    <span v-else class="material-symbols-outlined text-base">save</span>
                    {{ isEditing ? 'Salvar Alterações' : 'Salvar Produto' }}
                </button>
            </div>
        </template>

        <form id="category-form" @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- ── LEFT: Dados ─────────────────────────────── -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- Card dados -->
                    <div class="rounded-xl p-8 relative overflow-hidden" style="background-color: #f6f3ee;">
                        <div class="absolute top-0 right-0 opacity-5 pointer-events-none">
                            <span class="material-symbols-outlined" style="font-size: 120px;">inventory_2</span>
                        </div>
                        <h3 class="text-xl font-headline font-bold text-primary mb-8 flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary">description</span>
                            Dados do Produto
                        </h3>

                        <div class="space-y-6">
                            <!-- Categoria -->
                            <div class="space-y-2">
                                <label class="font-label text-[10px] uppercase tracking-widest text-outline block">Categoria</label>
                                <select v-model="form.product_category_id"
                                    class="w-full rounded-md p-4 font-body text-base outline-none transition-all focus:ring-2 focus:ring-primary-fixed-dim"
                                    style="background-color: #ebe8e3; border: none;">
                                    <option :value="null">— Sem categoria —</option>
                                    <option v-for="cat in productCategories" :key="cat.id" :value="cat.id">
                                        {{ cat.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.product_category_id" class="text-xs text-red-500">{{ form.errors.product_category_id }}</p>
                            </div>

                            <!-- Nomes -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="space-y-2">
                                    <label class="font-label text-[10px] uppercase tracking-widest text-outline block">Nome (PT) *</label>
                                    <input v-model="form.name_pt" required type="text"
                                        placeholder="Ex: Cercas de Design Contemporâneo"
                                        class="w-full rounded-md p-4 font-body text-lg outline-none transition-all focus:ring-2 focus:ring-primary-fixed-dim"
                                        style="background-color: #ebe8e3; border: none;" />
                                    <p v-if="form.errors.name_pt" class="text-xs text-red-500">{{ form.errors.name_pt }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="font-label text-[10px] uppercase tracking-widest text-outline block">Nome (EN) *</label>
                                    <input v-model="form.name_en" required type="text"
                                        placeholder="Ex: Contemporary Design Fences"
                                        class="w-full rounded-md p-4 font-body text-lg outline-none transition-all focus:ring-2 focus:ring-primary-fixed-dim"
                                        style="background-color: #ebe8e3; border: none;" />
                                    <p v-if="form.errors.name_en" class="text-xs text-red-500">{{ form.errors.name_en }}</p>
                                </div>
                            </div>

                            <!-- Descrição PT -->
                            <div class="space-y-2">
                                <label class="font-label text-[10px] uppercase tracking-widest text-outline block">Descrição (PT)</label>
                                <textarea v-model="form.description_pt" rows="4"
                                    placeholder="Descreva o produto e seus diferenciais..."
                                    class="w-full rounded-md p-4 font-body text-lg leading-relaxed outline-none resize-none transition-all focus:ring-2 focus:ring-primary-fixed-dim"
                                    style="background-color: #ebe8e3; border: none;"></textarea>
                                <p v-if="form.errors.description_pt" class="text-xs text-red-500">{{ form.errors.description_pt }}</p>
                            </div>

                            <!-- Descrição EN -->
                            <div class="space-y-2">
                                <label class="font-label text-[10px] uppercase tracking-widest text-outline block">Descrição (EN)</label>
                                <textarea v-model="form.description_en" rows="4"
                                    placeholder="Describe the product and its highlights..."
                                    class="w-full rounded-md p-4 font-body text-lg leading-relaxed outline-none resize-none transition-all focus:ring-2 focus:ring-primary-fixed-dim"
                                    style="background-color: #ebe8e3; border: none;"></textarea>
                                <p v-if="form.errors.description_en" class="text-xs text-red-500">{{ form.errors.description_en }}</p>
                            </div>

                            <!-- Toggle ativo -->
                            <div>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" v-model="form.is_active" class="sr-only" />
                                        <div class="w-11 h-6 rounded-full transition-colors"
                                            :style="form.is_active ? 'background-color:#012d1d;' : 'background-color:#c1c8c2;'"></div>
                                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform shadow"
                                            :style="form.is_active ? 'transform:translateX(20px);' : ''"></div>
                                    </div>
                                    <span class="font-label text-sm font-bold tracking-wide" style="color:#1c1c19;">
                                        {{ form.is_active ? 'Produto Ativo' : 'Produto Inativo' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Info cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-xl p-5" style="background-color: #f6f3ee;">
                            <p class="font-label text-[9px] uppercase tracking-[0.2em] text-outline mb-2">Padrão Visual</p>
                            <p class="font-body italic text-sm leading-relaxed" style="color: #414844;">Cada produto é exibido automaticamente na seção de segmentos da landing page.</p>
                        </div>
                        <div class="rounded-xl p-5" style="background-color: #f6f3ee;">
                            <p class="font-label text-[9px] uppercase tracking-[0.2em] text-outline mb-2">Otimização Bilíngue</p>
                            <p class="font-body italic text-sm leading-relaxed" style="color: #414844;">Preencha os dois idiomas para garantir a exibição correta em todas as versões do site.</p>
                        </div>
                        <div class="rounded-xl p-5" style="background-color: #f6f3ee;">
                            <p class="font-label text-[9px] uppercase tracking-[0.2em] text-outline mb-2">Produto Ativo</p>
                            <p class="font-body italic text-sm leading-relaxed" style="color: #414844;">Desative para ocultar o produto do site sem precisar excluí-lo do sistema.</p>
                        </div>
                    </div>
                </div>

                <!-- ── RIGHT: Galeria ───────────────────────────── -->
                <div class="lg:col-span-5 space-y-6">

                    <!-- Card galeria -->
                    <div class="rounded-xl p-8" style="background-color: #f6f3ee;">
                        <h3 class="text-xl font-headline font-bold text-primary mb-8 flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary">photo_library</span>
                            Galeria de Fotos
                        </h3>

                        <!-- Foto de capa -->
                        <div class="space-y-3 mb-6">
                            <p class="font-label text-[10px] uppercase tracking-widest text-outline">Foto de Capa</p>
                            <label class="cursor-pointer block group">
                                <div class="relative w-full rounded-lg overflow-hidden transition-all"
                                    style="height: 200px; background-color: #ebe8e3;">
                                    <img v-if="coverSrc" :src="coverSrc" class="w-full h-full object-cover" alt="Capa" />
                                    <div v-else class="w-full h-full flex flex-col items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-5xl" style="color: #c1c8c2;">add_a_photo</span>
                                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #c1c8c2;">Upload Cover</p>
                                    </div>
                                    <div v-if="coverSrc"
                                        class="absolute inset-0 flex flex-col items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                                        style="background-color: rgba(1,45,29,0.55);">
                                        <span class="material-symbols-outlined text-white text-4xl">add_a_photo</span>
                                        <p class="font-label text-[10px] uppercase tracking-widest text-white">Trocar foto</p>
                                    </div>
                                </div>
                                <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleCoverChange" />
                            </label>
                            <p v-if="form.errors.cover_photo" class="text-xs text-red-500">{{ form.errors.cover_photo }}</p>
                        </div>

                        <!-- Fotos adicionais -->
                        <div class="space-y-3">
                            <p class="font-label text-[10px] uppercase tracking-widest text-outline">Fotos Adicionais</p>
                            <PhotoUploader
                                v-model="form.new_photos"
                                :existing="category?.photos || []"
                                delete-url-prefix="/admin/portfolio/categoria-fotos"
                            />
                            <p v-if="form.errors['new_photos.*']" class="text-xs text-red-500">{{ form.errors['new_photos.*'] }}</p>
                        </div>
                    </div>

                    <!-- Nota -->
                    <div class="rounded-xl p-5 flex items-start gap-4" style="background-color: #fed3c7;">
                        <span class="material-symbols-outlined mt-0.5" style="color: #77574d; font-variation-settings: 'FILL' 1;">verified</span>
                        <div>
                            <p class="font-headline font-bold text-sm" style="color: #012d1d;">Dica de Qualidade</p>
                            <p class="font-body italic text-sm mt-1" style="color: #5d4037;">Use a foto de capa como imagem principal. Se não informada, a primeira foto da galeria será usada automaticamente.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
