<template>
    <div class="portal-card rounded-[28px] p-6">
        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ eyebrow }}</p>
                <h3 class="portal-display mt-2 text-2xl font-semibold text-slate-950">{{ title }}</h3>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">
                {{ normalizedLessons.length }} {{ t('common.items') }}
            </span>
        </div>

        <div v-if="normalizedLessons.length" class="space-y-3">
            <div
                v-for="lesson in normalizedLessons"
                :key="lesson.id"
                class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-4 lg:flex-row lg:items-center lg:justify-between"
            >
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                        {{ lesson.type === 'video' ? t('common.video') : 'PDF' }}
                    </p>
                    <h4 class="mt-2 text-base font-semibold text-slate-950">
                        {{ lesson.title }}
                    </h4>
                    <p class="mt-1 text-sm portal-muted">
                        {{ t('common.added') }} {{ lesson.created_at_display }}
                    </p>

                </div>

                <RouterLink
                    v-if="lessonRouteName"
                    :to="{ name: lessonRouteName, params: { id: lesson.id } }"
                    class="portal-button-primary"
                >
                    {{ actionLabel(lesson) }}
                </RouterLink>
            </div>
        </div>

        <EmptyState
            v-else
            :eyebrow="t('common.noData')"
            :title="t('spa.noLessonsTitle')"
            :description="t('spa.noLessonsDescription')"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';
import EmptyState from '@/src/components/shared/EmptyState.vue';
import { formatPortalDate } from '@/src/utils/formatters';

const props = defineProps({
    eyebrow: {
        type: String,
        default: '',
    },
    title: {
        type: String,
        default: '',
    },
    lessons: {
        type: Array,
        default: () => [],
    },
    lessonRouteName: {
        type: String,
        default: '',
    },
});

const { locale, t } = useI18n();
const normalizedLessons = computed(() =>
    props.lessons.map((lesson) => ({
        ...lesson,
        created_at_display: formatPortalDate(lesson.created_at, locale.value) || lesson.created_at_label || t('spa.recently'),
    })),
);

function actionLabel(lesson) {
    return lesson.type?.toLowerCase() === 'video' ? 'Play' : 'Read';
}
</script>
