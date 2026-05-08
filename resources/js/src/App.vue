<template>
    <RouterView v-slot="{ Component }">
        <Transition name="portal-fade" mode="out-in">
            <component :is="Component" />
        </Transition>
    </RouterView>
</template>

<script setup>
import { watch } from 'vue';
import { RouterView, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';

const route = useRoute();
const { locale, t } = useI18n({ useScope: 'global' });

watch(
    [() => route.fullPath, locale],
    () => {
        const routeTitle = typeof route.meta.titleKey === 'string'
            ? t(route.meta.titleKey)
            : t('common.appName');

        document.title = `${routeTitle} | ${t('common.appName')}`;
    },
    { immediate: true },
);
</script>
