import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useSettings() {
    const page = usePage();
    const settings = computed(() => page.props.settings || {});

    function setting(key, fallback = '') {
        return settings.value[key] ?? fallback;
    }

    // Parse company_units JSON
    const companyUnits = computed(() => {
        const raw = setting('company_units', null);
        if (raw) {
            try { return JSON.parse(raw); } catch { /* fall through */ }
        }
        return [];
    });

    // Primeira unidade marcada como matriz (ou a primeira de qualquer tipo)
    const matrizUnit = computed(() =>
        companyUnits.value.find(u => u.tipo === 'matriz') ?? companyUnits.value[0] ?? null
    );

    // WhatsApp vem sempre da matriz
    const whatsappNumber = computed(() =>
        matrizUnit.value?.whatsapp || setting('whatsapp_number', '554999600329')
    );
    const telegramLink = computed(() =>
        matrizUnit.value?.telegram || setting('telegram_link', 'https://t.me/trataplan')
    );
    const whatsappUrl = computed(() => `https://wa.me/${whatsappNumber.value}`);
    const logoTransparentUrl = computed(() => setting('logo_transparent_url', null));
    const logoWhiteBgUrl = computed(() => setting('logo_white_bg_url', null));

    return {
        settings, setting,
        companyUnits, matrizUnit,
        whatsappNumber, telegramLink, whatsappUrl,
        logoTransparentUrl, logoWhiteBgUrl,
    };
}
