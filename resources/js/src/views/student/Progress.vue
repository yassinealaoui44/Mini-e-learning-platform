<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.progressEyebrow')"
            :title="t('spa.progressTitle')"
            :description="t('spa.progressDescription')"
        />

        <ErrorState v-if="error" :message="error" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingProgress')" />

        <template v-else-if="progress">
            <div class="grid gap-4 md:grid-cols-3">
                <StatCard :label="t('common.courses')" :value="progress.stats.enrolled_courses" />
                <StatCard :label="t('common.lessons')" :value="progress.stats.total_lessons" />
                <StatCard :label="t('common.statistics')" :value="t('spa.statusPartial')" />
            </div>

            <div class="portal-alert-warning">
                {{ t('spa.progressNote') }}
            </div>

            <div class="grid gap-5 xl:grid-cols-2">
                <article
                    v-for="course in progress.courses"
                    :key="course.id"
                    class="portal-card rounded-[28px] p-6"
                >
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ course.track || t('spa.noTrack') }}</p>
                    <h3 class="portal-display mt-2 text-2xl font-semibold text-slate-950">{{ course.title }}</h3>
                    <p class="mt-2 text-sm portal-muted">{{ t('spa.progressCourseNote') }}</p>
                    <div class="mt-5 flex items-center justify-between gap-3 text-sm text-slate-700">
                        <span>{{ course.lessons_count }} {{ t('common.lessons').toLowerCase() }}</span>
                        <RouterLink :to="{ name: 'student-course-details', params: { id: course.id } }" class="font-semibold text-slate-950">
                            {{ t('common.openCourse') }}
                        </RouterLink>
                    </div>
                </article>
            </div>
        </template>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import StatCard from '@/src/components/shared/StatCard.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { portalApi } from '@/src/services/api';

const { t } = useI18n();
const { data: progress, loading, error, execute } = useAsyncState(null);

onMounted(() => {
    execute(() => portalApi.getStudentProgress());
});
</script>
