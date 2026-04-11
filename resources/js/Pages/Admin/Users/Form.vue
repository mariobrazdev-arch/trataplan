<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: null });

const props = defineProps({
    user: Object, // null = criar, object = editar
});

const isEditing = computed(() => !!props.user);

const form = useForm({
    name:                  props.user?.name     || '',
    email:                 props.user?.email    || '',
    is_admin:              props.user?.is_admin ?? true,
    password:              '',
    password_confirmation: '',
});

function submit() {
    if (isEditing.value) {
        form.patch(`/admin/usuarios/${props.user.id}`, {
            onSuccess: () => form.reset('password', 'password_confirmation'),
        });
    } else {
        form.post('/admin/usuarios');
    }
}
</script>

<template>
    <AdminLayout>
        <template #title>{{ isEditing ? 'Editar Usuário' : 'Novo Usuário' }}</template>
        <template #subtitle>{{ isEditing ? `Editando: ${user.name}` : 'Preencha os dados para criar um novo usuário.' }}</template>
        <template #actions>
            <Link href="/admin/usuarios"
                  class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-headline font-bold text-sm transition-all border"
                  style="color: #012d1d; border-color: #012d1d20; background: #f6f3ee;">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Voltar
            </Link>
        </template>

        <form @submit.prevent="submit" class="max-w-xl space-y-7">

            <!-- Nome -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #012d1d; font-family: Manrope, sans-serif;">
                    Nome completo *
                </label>
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Ex: João Silva"
                    class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                    style="border-color: #d6e8de; font-family: Manrope, sans-serif;"
                    :class="form.errors.name ? 'border-red-400 bg-red-50' : 'bg-white focus:border-emerald-600'"
                    required
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
            </div>

            <!-- E-mail -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #012d1d; font-family: Manrope, sans-serif;">
                    E-mail *
                </label>
                <input
                    v-model="form.email"
                    type="email"
                    placeholder="email@empresa.com"
                    class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                    style="border-color: #d6e8de; font-family: Manrope, sans-serif;"
                    :class="form.errors.email ? 'border-red-400 bg-red-50' : 'bg-white focus:border-emerald-600'"
                    required
                />
                <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
            </div>

            <!-- Tipo (admin) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest mb-3" style="color: #012d1d; font-family: Manrope, sans-serif;">
                    Nível de acesso
                </label>
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" v-model="form.is_admin" class="w-4 h-4 accent-emerald-700" />
                    <span class="text-sm" style="font-family: Manrope, sans-serif; color: #374151;">
                        Administrador (acesso total ao painel)
                    </span>
                </label>
            </div>

            <!-- Senha -->
            <div class="pt-2 border-t" style="border-color: #e8ede9;">
                <p class="text-xs font-bold uppercase tracking-widest mb-4" style="color: #012d1d; font-family: Manrope, sans-serif;">
                    {{ isEditing ? 'Alterar senha (deixe em branco para manter)' : 'Senha *' }}
                </p>
                <div class="space-y-4">
                    <div>
                        <input
                            v-model="form.password"
                            type="password"
                            placeholder="Mínimo 8 caracteres com letras e números"
                            class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                            style="border-color: #d6e8de; font-family: Manrope, sans-serif;"
                            :class="form.errors.password ? 'border-red-400 bg-red-50' : 'bg-white focus:border-emerald-600'"
                            :required="!isEditing"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                    </div>
                    <div>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="Confirmar senha"
                            class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition bg-white focus:border-emerald-600"
                            style="border-color: #d6e8de; font-family: Manrope, sans-serif;"
                            :required="!isEditing"
                        />
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex items-center gap-2 px-8 py-3 rounded-xl font-headline font-bold text-sm text-white shadow transition-all active:scale-95 disabled:opacity-60"
                    style="background-color: #012d1d;"
                >
                    <span class="material-symbols-outlined text-base">{{ isEditing ? 'save' : 'person_add' }}</span>
                    {{ isEditing ? 'Salvar Alterações' : 'Criar Usuário' }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
