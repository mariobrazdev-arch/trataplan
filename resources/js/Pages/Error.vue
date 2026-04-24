<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useSettings } from '@/composables/useSettings';

const props = defineProps({ status: Number });

const { logoTransparentUrl, logoWhiteBgUrl } = useSettings();
const logoUrl = computed(() => logoWhiteBgUrl.value || logoTransparentUrl.value);

const content = computed(() => {
    switch (props.status) {
        case 404: return {
            code: '404',
            icon: '🌲',
            title: 'Página não encontrada',
            description: 'A página que você está procurando não existe ou foi movida. Talvez o endereço esteja incorreto.',
            titleEn: 'Page not found',
        };
        case 403: return {
            code: '403',
            icon: '🔒',
            title: 'Acesso negado',
            description: 'Você não tem permissão para acessar esta página.',
            titleEn: 'Access denied',
        };
        case 419: return {
            code: '419',
            icon: '⏱️',
            title: 'Sessão expirada',
            description: 'Sua sessão expirou por inatividade. Por favor, atualize a página e tente novamente.',
            titleEn: 'Session expired',
            action: { label: 'Atualizar página', href: null, reload: true },
        };
        case 429: return {
            code: '429',
            icon: '🚦',
            title: 'Muitas tentativas',
            description: 'Você fez muitas requisições em pouco tempo. Aguarde alguns minutos e tente novamente.',
            titleEn: 'Too many requests',
        };
        case 500: return {
            code: '500',
            icon: '🔧',
            title: 'Erro interno do servidor',
            description: 'Algo inesperado aconteceu no nosso servidor. Nossa equipe já foi notificada. Tente novamente em instantes.',
            titleEn: 'Server error',
        };
        case 503: return {
            code: '503',
            icon: '🛠️',
            title: 'Em manutenção',
            description: 'O site está temporariamente fora do ar para manutenção. Voltaremos em breve.',
            titleEn: 'Service unavailable',
        };
        default: return {
            code: String(props.status ?? ''),
            icon: '⚙️',
            title: 'Ocorreu um erro',
            description: 'Algo não saiu como esperado. Por favor, tente novamente ou volte à página inicial.',
            titleEn: 'An error occurred',
        };
    }
});
</script>

<script>
export default { layout: null };
</script>

<template>
    <Head :title="`${content.code} — ${content.title}`" />

    <div style="min-height:100vh;background-color:#f0ede8;font-family:'Manrope',Arial,sans-serif;display:flex;flex-direction:column;">

        <!-- Navbar mínima -->
        <header style="background-color:#012d1d;padding:18px 32px;display:flex;align-items:center;justify-content:space-between;">
            <a href="/" style="text-decoration:none;display:flex;align-items:center;gap:12px;">
                <img v-if="logoUrl" :src="logoUrl" alt="Trataplan" style="height:36px;object-fit:contain;" />
                <span v-else style="font-size:20px;font-weight:900;letter-spacing:0.08em;color:#ffffff;text-transform:uppercase;">
                    TRATAPLAN
                </span>
            </a>
            <a href="/" style="font-size:13px;color:rgba(255,255,255,0.6);text-decoration:none;
                               font-weight:600;letter-spacing:0.05em;">
                Início
            </a>
        </header>

        <!-- Conteúdo principal -->
        <main style="flex:1;display:flex;align-items:center;justify-content:center;padding:48px 24px;">
            <div style="max-width:560px;width:100%;text-align:center;">

                <!-- Ícone decorativo -->
                <div style="font-size:64px;margin-bottom:16px;line-height:1;">
                    {{ content.icon }}
                </div>

                <!-- Código do erro -->
                <div style="font-size:96px;font-weight:900;color:#012d1d;line-height:1;
                            letter-spacing:-0.04em;margin-bottom:8px;">
                    {{ content.code }}
                </div>

                <!-- Linha dourada decorativa -->
                <div style="width:48px;height:4px;background-color:#fbbf24;border-radius:99px;
                            margin:0 auto 24px;"></div>

                <!-- Título -->
                <h1 style="font-size:24px;font-weight:800;color:#012d1d;margin:0 0 12px;line-height:1.3;">
                    {{ content.title }}
                </h1>

                <!-- Descrição -->
                <p style="font-size:15px;color:#717973;line-height:1.7;margin:0 0 36px;">
                    {{ content.description }}
                </p>

                <!-- Botões de ação -->
                <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                    <template v-if="content.action?.reload">
                        <button
                            @click="window.location.reload()"
                            style="display:inline-block;background-color:#fbbf24;color:#012d1d;
                                   font-size:13px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;
                                   text-decoration:none;padding:14px 28px;border-radius:10px;cursor:pointer;
                                   border:none;font-family:inherit;">
                            {{ content.action.label }}
                        </button>
                    </template>

                    <a href="/"
                       style="display:inline-block;background-color:#012d1d;color:#ffffff;
                              font-size:13px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;
                              text-decoration:none;padding:14px 28px;border-radius:10px;">
                        Voltar ao início
                    </a>

                    <a href="/contato"
                       style="display:inline-block;background-color:transparent;color:#012d1d;
                              font-size:13px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;
                              text-decoration:none;padding:14px 28px;border-radius:10px;
                              border:2px solid #012d1d33;">
                        Falar conosco
                    </a>
                </div>

            </div>
        </main>

        <!-- Rodapé mínimo -->
        <footer style="background-color:#012d1d;padding:20px 32px;text-align:center;">
            <p style="margin:0;font-size:12px;color:rgba(255,255,255,0.35);letter-spacing:0.05em;">
                © {{ new Date().getFullYear() }} Trataplan – Madeira Tratada. Todos os direitos reservados.
            </p>
        </footer>

    </div>
</template>
