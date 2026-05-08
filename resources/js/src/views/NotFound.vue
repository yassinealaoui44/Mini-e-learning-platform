<template>
    <div class="mx-auto max-w-3xl">
        <EmptyState
            :eyebrow="t('spa.notFoundEyebrow')"
            :title="t('spa.notFoundTitle')"
            :description="t('spa.notFoundDescription')"
        />
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
import EmptyState from '@/src/components/shared/EmptyState.vue';

const { t } = useI18n();
</script>
