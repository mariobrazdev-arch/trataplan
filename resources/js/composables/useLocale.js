import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useLocale() {
    const page = usePage();
    const locale = computed(() => page.props.locale || 'pt');
    const isPt = computed(() => locale.value === 'pt');
    const isEn = computed(() => locale.value === 'en');

    function t(ptText, enText) {
        return locale.value === 'en' ? enText : ptText;
    }

    function switchLocaleUrl(currentPath) {
        if (locale.value === 'en') {
            // en -> pt: remove /en prefix
            return currentPath.replace(/^\/en(\/|$)/, '/') || '/';
        } else {
            // pt -> en: add /en prefix
            return '/en' + (currentPath === '/' ? '' : currentPath);
        }
    }

    return { locale, isPt, isEn, t, switchLocaleUrl };
}
