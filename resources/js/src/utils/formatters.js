export function formatPortalDate(value, locale = 'en') {
    if (!value) {
        return '';
    }

    try {
        return new Intl.DateTimeFormat(locale, {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }).format(new Date(value));
    } catch {
        return '';
    }
}
