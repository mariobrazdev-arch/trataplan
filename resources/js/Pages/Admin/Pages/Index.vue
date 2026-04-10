<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

defineOptions({ layout: null });
defineProps({ pages: Array });
</script>

<template>
    <AdminLayout>
        <template #title>Páginas</template>
        <template #subtitle>Edite o conteúdo das páginas do site.</template>

        <!-- Section header -->
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-headline font-bold text-primary">Páginas do Site</h3>
            <span class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">{{ pages?.length || 0 }} páginas</span>
        </div>

        <!-- Cards -->
        <div class="space-y-6">
            <div v-for="page in pages" :key="page.id"
                class="group p-6 rounded-xl flex items-center gap-6 transition-all hover:translate-x-2"
                style="background-color: #ffffff;">

                <!-- Icon block -->
                <div class="w-32 h-32 flex-shrink-0 rounded-lg flex items-center justify-center" style="background-color: #f0ede8;">
                    <span class="material-symbols-outlined text-5xl group-hover:scale-110 transition-transform duration-500" style="color: #baaa79;">web</span>
                </div>

                <!-- Info -->
                <div class="flex-grow min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-2 py-0.5 rounded font-label text-[9px] uppercase font-black tracking-widest"
                            :style="page.is_published
                                ? 'background-color: #baaa79; color: #493f17;'
                                : 'background-color: #ebe8e3; color: #414844;'">
                            {{ page.is_published ? 'Publicada' : 'Oculta' }}
                        </span>
                        <span class="text-xs italic font-body" style="color: #717973;">Atualizada {{ page.updated_at }}</span>
                    </div>
                    <h4 class="text-xl font-headline font-extrabold text-primary">{{ page.title_pt }}</h4>
                    <p class="text-sm font-body italic mt-1" style="color: #6a5e33;">/{{ page.slug }}</p>
                </div>

                <!-- Actions (on hover) -->
                <div class="flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <Link :href="`/admin/paginas/${page.id}/editar`"
                        class="p-2 rounded-lg transition-colors" title="Editar"
                        style="color: #012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#c1ecd4'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined">edit_square</span>
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
