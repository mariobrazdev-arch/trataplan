<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineOptions({ layout: null });
defineProps({ services: Array });

function destroy(service) {
    if (confirm(`Remover "${service.name_pt}"?`)) {
        router.delete(`/admin/portfolio/servicos/${service.id}`);
    }
}
</script>

<template>
    <AdminLayout>
        <template #title>Mão de Obra</template>
        <template #subtitle>Serviços especializados exibidos na página inicial.</template>
        <template #actions>
            <Link href="/admin/portfolio/servicos/criar"
                class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95"
                style="background-color: #012d1d;">
                <span class="material-symbols-outlined text-base">add</span>
                Novo Serviço
            </Link>
        </template>

        <!-- Section header -->
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-headline font-bold text-primary">Serviços Cadastrados</h3>
            <span class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">{{ services?.length || 0 }} registros</span>
        </div>

        <!-- Empty -->
        <div v-if="!services?.length" class="py-20 text-center rounded-xl" style="background-color: #f6f3ee;">
            <span class="material-symbols-outlined text-5xl mb-3 block" style="color: #c1c8c2;">construction</span>
            <p class="font-body italic" style="color: #717973;">Nenhum serviço cadastrado ainda.</p>
            <Link href="/admin/portfolio/servicos/criar" class="mt-4 inline-block font-label text-sm font-bold uppercase tracking-widest hover:underline" style="color: #012d1d;">Criar primeiro serviço</Link>
        </div>

        <!-- Cards -->
        <div v-else class="space-y-6">
            <div v-for="service in services" :key="service.id"
                class="group p-6 rounded-xl flex items-center gap-6 transition-all hover:translate-x-2"
                style="background-color: #ffffff;">

                <!-- Thumbnail -->
                <div class="w-32 h-32 flex-shrink-0 rounded-lg overflow-hidden" style="background-color: #f0ede8;">
                    <img v-if="service.cover_url"
                        :src="service.cover_url"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-4xl" style="color: #c1c8c2;">construction</span>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex-grow min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-2 py-0.5 rounded font-label text-[9px] uppercase font-black tracking-widest"
                            :style="service.is_active
                                ? 'background-color: #baaa79; color: #493f17;'
                                : 'background-color: #ebe8e3; color: #414844;'">
                            {{ service.is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                        <span class="text-xs italic font-body" style="color: #717973;">Ordem {{ service.order }}</span>
                    </div>
                    <h4 class="text-xl font-headline font-extrabold text-primary">{{ service.name_pt }}</h4>
                    <p class="text-sm font-body italic mt-1" style="color: #414844;">{{ service.name_en }}</p>
                    <p v-if="service.description_pt" class="text-sm font-body line-clamp-2 mt-1 italic" style="color: #717973;">{{ service.description_pt }}</p>
                    <div class="flex items-center gap-6 mt-4">
                        <div class="flex items-center gap-1" style="color: #717973;">
                            <span class="material-symbols-outlined text-sm">photo_library</span>
                            <span class="text-[10px] font-label font-bold uppercase tracking-tighter">{{ service.photos_count }} Fotos</span>
                        </div>
                    </div>
                </div>

                <!-- Actions (on hover) -->
                <div class="flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <Link :href="`/admin/portfolio/servicos/${service.id}/editar`"
                        class="p-2 rounded-lg transition-colors" title="Editar"
                        style="color: #012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#c1ecd4'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined">edit_square</span>
                    </Link>
                    <button @click="destroy(service)"
                        class="p-2 rounded-lg transition-colors" title="Remover"
                        style="color: #ba1a1a;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#ffdad6'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
