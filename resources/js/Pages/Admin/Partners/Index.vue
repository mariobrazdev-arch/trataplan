<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from '@/composables/useConfirm';

defineOptions({ layout: null });
defineProps({ partners: Array });

const { confirm } = useConfirm();

async function destroy(partner) {
    const ok = await confirm({ title: 'Remover parceiro', message: `Remover "${partner.name}"? Esta ação não pode ser desfeita.` });
    if (!ok) return;
    router.delete(`/admin/parceiros/${partner.id}`);
}

function initials(name) {
    return name?.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase() || '?';
}
</script>

<template>
    <AdminLayout>
        <template #title>Parceiros</template>
        <template #subtitle>Empresas e colaboradores parceiros da Trataplan.</template>
        <template #actions>
            <Link href="/admin/parceiros/criar"
                class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95"
                style="background-color: #012d1d;">
                <span class="material-symbols-outlined text-base">add</span>
                Novo Parceiro
            </Link>
        </template>

        <!-- Section header -->
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-headline font-bold text-primary">Parceiros Ativos</h3>
            <span class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">{{ partners?.length || 0 }} registros</span>
        </div>

        <!-- Empty -->
        <div v-if="!partners?.length" class="py-20 text-center rounded-xl" style="background-color: #f6f3ee;">
            <span class="material-symbols-outlined text-5xl mb-3 block" style="color: #c1c8c2;">handshake</span>
            <p class="font-body italic" style="color: #717973;">Nenhum parceiro cadastrado ainda.</p>
            <Link href="/admin/parceiros/criar" class="mt-4 inline-block font-label text-sm font-bold uppercase tracking-widest hover:underline" style="color: #012d1d;">Adicionar parceiro</Link>
        </div>

        <!-- Cards -->
        <div v-else class="space-y-6">
            <div v-for="partner in partners" :key="partner.id"
                class="group p-6 rounded-xl flex items-center gap-6 transition-all hover:translate-x-2"
                style="background-color: #ffffff;">

                <!-- Logo / Avatar -->
                <div class="w-32 h-32 flex-shrink-0 rounded-lg overflow-hidden flex items-center justify-center" style="background-color: #f0ede8;">
                    <img v-if="partner.logo_url"
                        :src="partner.logo_url"
                        class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-500 p-3" />
                    <span v-else class="text-2xl font-headline font-black" style="color: #c1c8c2;">{{ initials(partner.name) }}</span>
                </div>

                <!-- Info -->
                <div class="flex-grow min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-2 py-0.5 rounded font-label text-[9px] uppercase font-black tracking-widest"
                            :style="partner.is_active
                                ? 'background-color: #baaa79; color: #493f17;'
                                : 'background-color: #ebe8e3; color: #414844;'">
                            {{ partner.is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                        <span class="text-xs italic font-body" style="color: #717973;">Ordem {{ partner.order }}</span>
                    </div>
                    <h4 class="text-xl font-headline font-extrabold text-primary">{{ partner.name }}</h4>
                    <a v-if="partner.website" :href="partner.website" target="_blank"
                        class="text-sm font-body italic mt-1 hover:underline block truncate"
                        style="color: #6a5e33;">{{ partner.website }}</a>
                    <p v-if="partner.description" class="text-sm font-body line-clamp-2 mt-1 italic" style="color: #717973;">{{ partner.description }}</p>
                </div>

                <!-- Actions (on hover) -->
                <div class="flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <Link :href="`/admin/parceiros/${partner.id}/editar`"
                        class="p-2 rounded-lg transition-colors" title="Editar"
                        style="color: #012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#c1ecd4'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined">edit_square</span>
                    </Link>
                    <button @click="destroy(partner)"
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
