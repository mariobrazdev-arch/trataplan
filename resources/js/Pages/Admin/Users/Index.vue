<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from '@/composables/useConfirm';

defineOptions({ layout: null });

const props = defineProps({
    users: Array,
});

const { confirm } = useConfirm();

async function remove(id) {
    const ok = await confirm({ title: 'Excluir usuário', message: 'Excluir este usuário? Esta ação não pode ser desfeita.' });
    if (!ok) return;
    router.delete(`/admin/usuarios/${id}`, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout>
        <template #title>Usuários</template>
        <template #subtitle>Gerencie os usuários com acesso ao painel administrativo.</template>
        <template #actions>
            <Link href="/admin/usuarios/criar"
                  class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95"
                  style="background-color: #012d1d;">
                <span class="material-symbols-outlined text-base">person_add</span>
                Novo Usuário
            </Link>
        </template>

        <!-- Cabeçalho -->
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-headline font-bold" style="color: #012d1d;">Lista de Usuários</h3>
            <span class="font-label text-[10px] uppercase tracking-[0.2em]" style="color: #9aaba0;">
                {{ users?.length || 0 }} usuários
            </span>
        </div>

        <!-- Vazio -->
        <div v-if="!users?.length" class="py-20 text-center rounded-xl" style="background-color: #f6f3ee;">
            <span class="material-symbols-outlined text-5xl mb-3 block" style="color: #c1c8c2;">group</span>
            <p class="font-body italic" style="color: #717973;">Nenhum usuário cadastrado.</p>
        </div>

        <!-- Cards -->
        <div v-else class="space-y-3">
            <div
                v-for="user in users"
                :key="user.id"
                class="group p-5 rounded-xl flex items-center gap-5 transition-all"
                style="background-color: #f6f3ee; border: 1px solid transparent;"
                onmouseover="this.style.borderColor='#d6e8de'"
                onmouseout="this.style.borderColor='transparent'"
            >
                <!-- Avatar -->
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 font-headline font-bold text-sm text-white select-none"
                     style="background-color: #77574d;">
                    {{ user.name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase() }}
                </div>

                <!-- Info -->
                <div class="flex-grow min-w-0">
                    <p class="font-headline font-bold text-sm truncate" style="color: #012d1d;">{{ user.name }}</p>
                    <p class="text-xs truncate mt-0.5" style="color: #717973; font-family: Manrope, sans-serif;">{{ user.email }}</p>
                </div>

                <!-- Badge admin -->
                <span
                    v-if="user.is_admin"
                    class="hidden sm:inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full"
                    style="background-color: #e6f4eb; color: #012d1d;"
                >
                    <span class="material-symbols-outlined text-xs leading-none">admin_panel_settings</span>
                    Admin
                </span>
                <span v-else
                    class="hidden sm:inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full"
                    style="background-color: #f0ebe3; color: #77574d;"
                >
                    <span class="material-symbols-outlined text-xs leading-none">person</span>
                    Usuário
                </span>

                <!-- Data -->
                <span class="hidden md:block text-xs flex-shrink-0" style="color: #9aaba0; font-family: Manrope, sans-serif;">
                    {{ user.created_at }}
                </span>

                <!-- Ações -->
                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <Link
                        :href="`/admin/usuarios/${user.id}/editar`"
                        class="p-2 rounded-lg transition-colors hover:bg-emerald-100"
                        title="Editar"
                    >
                        <span class="material-symbols-outlined text-lg" style="color: #012d1d;">edit</span>
                    </Link>
                    <button
                        @click="remove(user.id)"
                        class="p-2 rounded-lg transition-colors hover:bg-red-100"
                        title="Excluir"
                    >
                        <span class="material-symbols-outlined text-lg text-red-500">delete</span>
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
