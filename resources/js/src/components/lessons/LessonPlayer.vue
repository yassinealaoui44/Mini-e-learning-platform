<template>
    <div class="portal-card rounded-[28px] p-6">
        <div class="mb-5">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ lesson.type === 'video' ? t('common.video') : 'PDF' }}</p>
            <h3 class="portal-display mt-2 text-3xl font-semibold text-slate-950">{{ lesson.title }}</h3>
            <p class="mt-2 text-sm portal-muted">
                {{ lesson.course?.title || t('common.lesson') }}
            </p>
        </div>

        <div v-if="lesson.type === 'video' && lesson.file_url" class="overflow-hidden rounded-[24px] border border-slate-200 bg-slate-950">
            <video
                :src="lesson.file_url"
                controls
                controlsList="nodownload"
                class="h-full w-full"
            />
        </div>

        <div
            v-else-if="lesson.type === 'pdf' && lesson.file_url"
            class="overflow-hidden rounded-[24px] border border-slate-200 bg-white"
        >
            <iframe :src="lesson.file_url" class="h-[70vh] w-full" :title="t('common.lesson')" />
        </div>

        <div v-else class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-5 py-6">
            <p class="text-sm portal-muted">
                {{ t('spa.inlinePreviewUnavailable') }}
            </p>
            <a
                v-if="lesson.file_url"
                :href="lesson.file_url"
                target="_blank"
                rel="noreferrer"
                class="portal-button-primary mt-4"
            >
                {{ t('spa.openFileNewTab') }}
            </a>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';

defineProps({
    lesson: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();
</script>
