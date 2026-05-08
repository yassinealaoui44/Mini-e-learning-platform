import { computed } from 'vue';
import { createI18n, useI18n } from 'vue-i18n';
import { messages } from '@/i18n/messages';
import { setApiLocale } from '@/src/services/api';

const STORAGE_KEY = 'academyhub.locale';
const COOKIE_KEY = 'academyhub_locale';
const SUPPORTED_LOCALES = ['en', 'fr'];

function resolveInitialLocale() {
    if (typeof window === 'undefined') {
        return 'en';
    }

    const stored = window.localStorage.getItem(STORAGE_KEY);

    if (stored && SUPPORTED_LOCALES.includes(stored)) {
        return stored;
    }

    const browserLocale = window.navigator.language?.slice(0, 2)?.toLowerCase();

    return SUPPORTED_LOCALES.includes(browserLocale) ? browserLocale : 'en';
}

const i18n = createI18n({
    legacy: false,
    locale: resolveInitialLocale(),
    fallbackLocale: 'en',
    messages,
});

export function applyDocumentLocale(locale) {
    if (typeof document === 'undefined') {
        return;
    }

    document.documentElement.lang = locale;
}

function applyRequestLocale(locale) {
    if (typeof window === 'undefined') {
        return;
    }

    if (window.axios) {
        window.axios.defaults.headers.common['X-Locale'] = locale;
    }

    setApiLocale(locale);

    document.cookie = `${COOKIE_KEY}=${locale}; path=/; max-age=31536000; samesite=lax`;
}

export function setLocale(locale) {
    const normalized = SUPPORTED_LOCALES.includes(locale) ? locale : 'en';

    i18n.global.locale.value = normalized;
    applyDocumentLocale(normalized);
    applyRequestLocale(normalized);

    if (typeof window !== 'undefined') {
        window.localStorage.setItem(STORAGE_KEY, normalized);
    }
}

export function installI18n(app) {
    app.use(i18n);
    setLocale(i18n.global.locale.value);
}

export function useLocale() {
    const { locale, t } = useI18n({ useScope: 'global' });

    const currentLocale = computed(() => locale.value);
    const localeOptions = computed(() => [
        { value: 'en', label: t('common.english') },
        { value: 'fr', label: t('common.french') },
    ]);

    return {
        locale: currentLocale,
        localeOptions,
        setLocale,
    };
}

export default i18n;
