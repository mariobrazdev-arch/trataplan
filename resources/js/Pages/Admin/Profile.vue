<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';

defineOptions({ layout: null });

const props = defineProps({
    user: Object,
});

const form = useForm({
    name:                  props.user?.name  || '',
    email:                 props.user?.email || '',
    current_password:      '',
    password:              '',
    password_confirmation: '',
});

function submit() {
    form.patch('/admin/perfil', {
        onSuccess: () => form.reset('current_password', 'password', 'password_confirmation'),
    });
}
</script>

<template>
    <AdminLayout>
        <template #title>Meu Perfil</template>
        <template #subtitle>Atualize seus dados de acesso ao painel.</template>

        <div class="max-w-xl space-y-10">

            <!-- Dados pessoais -->
            <form @submit.prevent="submit" class="space-y-7">

                <!-- Avatar placeholder -->
                <div class="flex items-center gap-5 p-5 rounded-xl" style="background-color: #f6f3ee;">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center font-headline font-black text-xl text-white flex-shrink-0"
                         style="background-color: #77574d;">
                        {{ form.name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase() || '?' }}
                    </div>
                    <div>
                        <p class="font-headline font-bold" style="color: #012d1d;">{{ form.name || 'Nome' }}</p>
                        <p class="text-xs mt-0.5" style="color: #9aaba0; font-family: Manrope, sans-serif;">{{ form.email }}</p>
                    </div>
                </div>

                <!-- Nome -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #012d1d; font-family: Manrope, sans-serif;">
                        Nome completo *
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
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
                        class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                        style="border-color: #d6e8de; font-family: Manrope, sans-serif;"
                        :class="form.errors.email ? 'border-red-400 bg-red-50' : 'bg-white focus:border-emerald-600'"
                        required
                    />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                </div>

                <!-- Trocar senha -->
                <div class="pt-2 border-t" style="border-color: #e8ede9;">
                    <p class="text-xs font-bold uppercase tracking-widest mb-4" style="color: #012d1d; font-family: Manrope, sans-serif;">
                        Alterar senha <span class="normal-case font-normal tracking-normal text-gray-400">(deixe em branco para manter)</span>
                    </p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs mb-1.5" style="color: #717973; font-family: Manrope, sans-serif;">Senha atual</label>
                            <input
                                v-model="form.current_password"
                                type="password"
                                placeholder="••••••••"
                                class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition bg-white focus:border-emerald-600"
                                style="border-color: #d6e8de; font-family: Manrope, sans-serif;"
                                :class="form.errors.current_password ? 'border-red-400 bg-red-50' : ''"
                            />
                            <p v-if="form.errors.current_password" class="mt-1 text-xs text-red-500">{{ form.errors.current_password }}</p>
                        </div>
                        <div>
                            <label class="block text-xs mb-1.5" style="color: #717973; font-family: Manrope, sans-serif;">Nova senha</label>
                            <input
                                v-model="form.password"
                                type="password"
                                placeholder="Mínimo 8 caracteres com letras e números"
                                class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition bg-white focus:border-emerald-600"
                                style="border-color: #d6e8de; font-family: Manrope, sans-serif;"
                                :class="form.errors.password ? 'border-red-400 bg-red-50' : ''"
                            />
                            <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                        </div>
                        <div>
                            <label class="block text-xs mb-1.5" style="color: #717973; font-family: Manrope, sans-serif;">Confirmar nova senha</label>
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                placeholder="••••••••"
                                class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition bg-white focus:border-emerald-600"
                                style="border-color: #d6e8de; font-family: Manrope, sans-serif;"
                            />
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex items-center gap-2 px-8 py-3 rounded-xl font-headline font-bold text-sm text-white shadow transition-all active:scale-95 disabled:opacity-60"
                        style="background-color: #012d1d;"
                    >
                        <span class="material-symbols-outlined text-base">save</span>
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
