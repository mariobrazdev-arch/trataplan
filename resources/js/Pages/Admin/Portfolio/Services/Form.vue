<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import PhotoUploader from '@/Components/Admin/PhotoUploader.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref } from 'vue';

defineOptions({ layout: null });

const props = defineProps({ service: Object });
const isEditing = computed(() => !!props.service);

const form = useForm({
    name_pt:         props.service?.name_pt || '',
    description_pt:  props.service?.description_pt || '',
    cover_photo:     null,
    order:           props.service?.order ?? 0,
    is_active:       props.service?.is_active ?? true,
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
    const opts = { forceFormData: true };
    if (isEditing.value) {
        form.transform(d => ({ ...d, _method: 'PATCH' }))
            .post(`/admin/portfolio/servicos/${props.service.id}`, opts);
    } else {
        form.post('/admin/portfolio/servicos', opts);
    }
}

const coverSrc = computed(() => coverPreview.value || props.service?.cover_url || props.service?.photos?.[0]?.url || null);
</script>

<template>
    <AdminLayout>
        <template #breadcrumb>Mão de Obra</template>
        <template #title>{{ isEditing ? 'Editar Serviço' : 'Cadastro de Mão de Obra' }}</template>
        <template #actions>
            <div class="flex items-center gap-3">
                <a href="/admin/portfolio/servicos"
                    class="px-5 py-2.5 rounded-lg font-headline font-bold text-sm transition-colors"
                    style="color: #717973; background-color: #ebe8e3;">
                    Cancelar
                </a>
                <button type="submit" form="service-form" :disabled="form.processing"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95 disabled:opacity-60"
                    style="background-color: #012d1d;">
                    <span v-if="form.processing" class="material-symbols-outlined text-base animate-spin">autorenew</span>
                    <span v-else class="material-symbols-outlined text-base">save</span>
                    {{ isEditing ? 'Salvar Alterações' : 'Salvar Serviço' }}
                </button>
            </div>
        </template>

        <form id="service-form" @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- ── LEFT: Dados ─────────────────────────────── -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- Card dados -->
                    <div class="rounded-xl p-8 relative overflow-hidden" style="background-color: #f6f3ee;">
                        <div class="absolute top-0 right-0 opacity-5 pointer-events-none">
                            <span class="material-symbols-outlined" style="font-size: 120px;">construction</span>
                        </div>
                        <h3 class="text-xl font-headline font-bold text-primary mb-8 flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary">description</span>
                            Dados do Serviço
                        </h3>

                        <div class="space-y-6">
                            <!-- Nome -->
                            <div class="space-y-2">
                                <label class="font-label text-[10px] uppercase tracking-widest text-outline block">Nome *</label>
                                <input v-model="form.name_pt" required type="text"
                                    placeholder="Ex: Montagem de Estruturas"
                                    class="w-full rounded-md p-4 font-body text-lg outline-none transition-all focus:ring-2 focus:ring-primary-fixed-dim"
                                    style="background-color: #ebe8e3; border: none;" />
                                <p v-if="form.errors.name_pt" class="text-xs text-red-500">{{ form.errors.name_pt }}</p>
                            </div>

                            <!-- Descrição -->
                            <div class="space-y-2">
                                <label class="font-label text-[10px] uppercase tracking-widest text-outline block">Descrição</label>
                                <textarea v-model="form.description_pt" rows="4"
                                    placeholder="Descreva os detalhes técnicos do serviço..."
                                    class="w-full rounded-md p-4 font-body text-lg leading-relaxed outline-none resize-none transition-all focus:ring-2 focus:ring-primary-fixed-dim"
                                    style="background-color: #ebe8e3; border: none;"></textarea>
                                <p v-if="form.errors.description_pt" class="text-xs text-red-500">{{ form.errors.description_pt }}</p>
                            </div>

                            <!-- Ordem + Toggle -->
                            <div class="flex items-end gap-6">
                                <div class="space-y-2">
                                    <label class="font-label text-[10px] uppercase tracking-widest text-outline block">Ordem</label>
                                    <input v-model="form.order" type="number" min="0"
                                        class="w-24 rounded-md p-4 font-body text-lg outline-none transition-all focus:ring-2 focus:ring-primary-fixed-dim"
                                        style="background-color: #ebe8e3; border: none;" />
                                </div>
                                <label class="flex items-center gap-3 cursor-pointer pb-3">
                                    <div class="relative">
                                        <input type="checkbox" v-model="form.is_active" class="sr-only" />
                                        <div class="w-11 h-6 rounded-full transition-colors"
                                            :style="form.is_active ? 'background-color:#012d1d;' : 'background-color:#c1c8c2;'"></div>
                                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform shadow"
                                            :style="form.is_active ? 'transform:translateX(20px);' : ''"></div>
                                    </div>
                                    <span class="font-label text-sm font-bold tracking-wide" style="color:#1c1c19;">
                                        {{ form.is_active ? 'Serviço Ativo' : 'Serviço Inativo' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Info cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-xl p-5" style="background-color: #f6f3ee;">
                            <p class="font-label text-[9px] uppercase tracking-[0.2em] text-outline mb-2">Padrão Editorial</p>
                            <p class="font-body italic text-sm leading-relaxed" style="color: #414844;">Cada serviço é exibido automaticamente na seção de Mão de Obra da landing page.</p>
                        </div>
                        <div class="rounded-xl p-5" style="background-color: #f6f3ee;">
                            <p class="font-label text-[9px] uppercase tracking-[0.2em] text-outline mb-2">Controle de Ordem</p>
                            <p class="font-body italic text-sm leading-relaxed" style="color: #414844;">A ordem numérica define a prioridade de exibição no carrossel da página inicial.</p>
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
                            <div class="flex items-center justify-between">
                                <p class="font-label text-[10px] uppercase tracking-widest text-outline">Fotos Adicionais</p>
                            </div>
                            <PhotoUploader
                                v-model="form.new_photos"
                                :existing="service?.photos || []"
                                delete-url-prefix="/admin/portfolio/servico-fotos"
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
