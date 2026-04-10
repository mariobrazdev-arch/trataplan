<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash);
const sidebarOpen = ref(false);

const navItems = [
    { label: 'Dashboard', href: '/admin', icon: 'dashboard' },
    { label: 'Postagens', href: '/admin/posts', icon: 'article' },
    { label: 'Páginas', href: '/admin/paginas', icon: 'web' },
    { label: 'Parceiros', href: '/admin/parceiros', icon: 'handshake' },
    { separator: true, label: 'Portfólio' },
    { label: 'Mão de Obra', href: '/admin/portfolio/servicos', icon: 'construction' },
    { label: 'Produtos', href: '/admin/portfolio/categorias', icon: 'inventory_2' },
    { separator: true },
    { label: 'Mensagens', href: '/admin/mensagens', icon: 'mail' },
    { label: 'Configurações', href: '/admin/configuracoes', icon: 'settings' },
];

const userInitials = computed(() => {
    const name = user.value?.name || '';
    return name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
});

function logout() {
    router.post('/admin/logout');
}

function isActive(href) {
    const path = window.location.pathname;
    if (href === '/admin') return path === '/admin';
    return path.startsWith(href);
}
</script>

<template>
    <div class="min-h-screen bg-background font-body text-on-background selection:bg-primary-fixed selection:text-on-primary-fixed">

        <!-- Sidebar -->
        <aside
            class="h-screen w-64 fixed left-0 top-0 flex flex-col p-6 space-y-4 shadow-2xl z-50 transition-transform duration-300 md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            style="background-color: #012d1d;"
        >
            <!-- Brand -->
            <div class="mb-4">
                <h1 class="text-lg font-bold text-white font-headline">Admin Console</h1>
                <p class="font-label text-[10px] uppercase tracking-widest text-emerald-100/50 mt-0.5">Management Suite</p>
            </div>

            <!-- User profile -->
            <div class="flex items-center space-x-3 mb-6 px-2">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 font-headline font-bold text-sm text-white" style="background-color: #77574d;">
                    {{ userInitials }}
                </div>
                <div class="min-w-0">
                    <p class="text-white text-xs font-headline font-bold truncate">{{ user?.name }}</p>
                    <p class="text-emerald-100/50 text-[10px] font-label uppercase tracking-wide">Administrador</p>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-grow space-y-1 overflow-y-auto">
                <template v-for="item in navItems" :key="item.href ?? item.label">
                    <template v-if="item.separator">
                        <div v-if="item.label" class="pt-3 pb-1">
                            <span class="font-label text-[9px] uppercase tracking-[0.2em] font-bold" style="color: rgba(255,255,255,0.25);">{{ item.label }}</span>
                        </div>
                        <hr v-else class="my-2 border-emerald-800/30" />
                    </template>
                    <Link
                        v-else
                        :href="item.href"
                        class="flex items-center space-x-3 p-3 rounded-md font-label text-sm uppercase tracking-widest transition-all duration-200 ease-in-out"
                        :class="isActive(item.href)
                            ? 'text-white shadow-inner'
                            : 'text-emerald-100/70 hover:bg-emerald-800/50 hover:text-white'"
                        :style="isActive(item.href) ? 'background-color: #77574d;' : ''"
                        @click="sidebarOpen = false"
                    >
                        <span class="material-symbols-outlined text-xl">{{ item.icon }}</span>
                        <span>{{ item.label }}</span>
                    </Link>
                </template>
            </nav>

            <!-- Bottom actions -->
            <div class="pt-6 border-t border-emerald-800/30 space-y-1">
                <Link
                    href="/"
                    target="_blank"
                    class="w-full flex items-center justify-center gap-2 mb-3 p-3 rounded-md font-headline font-bold text-sm text-white transition-all"
                    style="background-color: rgba(255,255,255,0.08);"
                >
                    <span class="material-symbols-outlined text-base">open_in_new</span>
                    Ver Site
                </Link>
                <button
                    @click="logout"
                    class="flex items-center space-x-3 p-3 w-full text-emerald-100/70 hover:bg-emerald-800/50 hover:text-white rounded-md transition-all duration-200 font-label text-sm uppercase tracking-widest"
                >
                    <span class="material-symbols-outlined">logout</span>
                    <span>Sair</span>
                </button>
            </div>
        </aside>

        <!-- Overlay (mobile) -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-black/50 md:hidden"
            @click="sidebarOpen = false"
        ></div>

        <!-- Main canvas -->
        <main class="md:ml-64 p-6 md:p-12 min-h-screen">

            <!-- Mobile menu toggle -->
            <button class="md:hidden mb-6 p-1 text-primary" @click="sidebarOpen = !sidebarOpen">
                <span class="material-symbols-outlined">menu</span>
            </button>

            <!-- Page header -->
            <header class="mb-12 flex justify-between items-end flex-wrap gap-4">
                <div>
                    <p v-if="$slots.breadcrumb" class="font-label text-[10px] uppercase tracking-[0.2em] text-outline mb-2">
                        <slot name="breadcrumb" />
                    </p>
                    <h2 class="text-4xl font-headline font-extrabold text-primary tracking-tight">
                        <slot name="title">Admin</slot>
                    </h2>
                    <p v-if="$slots.subtitle" class="text-tertiary italic text-lg mt-1">
                        <slot name="subtitle"></slot>
                    </p>
                </div>
                <slot name="actions">
                    <div class="text-right hidden sm:block">
                        <span class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">Status do Sistema</span>
                        <div class="flex items-center gap-2 text-primary font-bold justify-end mt-1">
                            <div class="w-2 h-2 rounded-full animate-pulse" style="background-color: #6a5e33;"></div>
                            <span class="font-label text-xs uppercase tracking-widest">Online</span>
                        </div>
                    </div>
                </slot>
            </header>

            <!-- Flash messages -->
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 translate-y-[-8px]"
                enter-to-class="opacity-100 translate-y-0"
            >
                <div v-if="flash?.success"
                    class="mb-8 p-4 rounded-xl flex items-center gap-3"
                    style="background-color: #c1ecd4; color: #012d1d;">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    {{ flash.success }}
                </div>
            </Transition>

            <!-- Page content -->
            <slot />

        </main>
    </div>
</template>
