<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';

defineOptions({ layout: null }); // No AppLayout for login

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/admin/login');
}
</script>

<template>
    <Head><title>Admin – Trataplan</title></Head>

    <div class="min-h-screen flex items-center justify-center p-6" style="background-color: #012d1d;">
        <div class="w-full max-w-md">
            <div class="text-center mb-10">
                <span class="material-symbols-outlined text-emerald-400 text-5xl mb-3 block" style="font-variation-settings: 'FILL' 1;">forest</span>
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter" style="font-family: Manrope, sans-serif;">TRATAPLAN</h1>
                <p class="text-emerald-200/60 text-sm mt-1" style="font-family: Manrope, sans-serif;">Painel Administrativo</p>
            </div>

            <div class="rounded-2xl p-8 shadow-2xl" style="background-color: #fcf9f4;">
                <h2 class="text-xl font-bold mb-6" style="font-family: Manrope, sans-serif; color: #012d1d;">Entrar</h2>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="font-family: Manrope, sans-serif; color: #77574d;">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            class="w-full px-4 py-3 rounded-lg outline-none focus:ring-2"
                            style="background-color: #ebe8e3; border: none; font-family: Newsreader, serif; color: #1c1c19;"
                            placeholder="admin@trataplan.com.br"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm" style="color: #ba1a1a;">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="font-family: Manrope, sans-serif; color: #77574d;">Senha</label>
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            class="w-full px-4 py-3 rounded-lg outline-none focus:ring-2"
                            style="background-color: #ebe8e3; border: none; font-family: Newsreader, serif; color: #1c1c19;"
                            placeholder="••••••••"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-sm" style="color: #ba1a1a;">{{ form.errors.password }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3 rounded-xl font-bold text-white transition-all hover:opacity-90 disabled:opacity-60"
                        style="font-family: Manrope, sans-serif; background-color: #012d1d;"
                    >
                        {{ form.processing ? 'Entrando...' : 'Entrar' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
