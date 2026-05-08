<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.analyticsEyebrow')"
            :title="t('spa.analyticsTitle')"
            :description="t('spa.analyticsDescription')"
        />

        <ErrorState v-if="error" :message="error" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingAnalytics')" />

        <template v-else-if="analytics">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <StatCard :label="t('common.courses')" :value="analytics.summary.courses" />
                <StatCard :label="t('common.lessons')" :value="analytics.summary.lessons" />
                <StatCard label="Monthly Enrollments" :value="analytics.summary.monthly_enrollments" />
                <StatCard label="Yearly Enrollments" :value="analytics.summary.yearly_enrollments" />
            </div>

            <div class="grid gap-5 xl:grid-cols-2">
                <article class="portal-card rounded-[28px] p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ t('spa.topCourses') }}</p>
                    <div class="mt-5 space-y-4">
                        <div
                            v-for="course in analytics.top_courses"
                            :key="course.id"
                            class="rounded-2xl border border-slate-200 bg-white px-4 py-4"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-base font-semibold text-slate-950">{{ course.title }}</h3>
                                    <p class="mt-1 text-sm portal-muted">{{ course.track || t('spa.noTrack') }}</p>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">
                                    {{ course.students_count }} {{ t('common.students').toLowerCase() }}
                                </span>
                            </div>
                            <p class="mt-3 text-sm text-slate-700">
                                {{ course.lessons_count }} {{ t('common.lessons').toLowerCase() }}
                            </p>
                        </div>
                    </div>
                </article>

                <article class="portal-card rounded-[28px] p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Enrollment trends</p>
                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Students</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-950">{{ analytics.summary.students }}</p>
                            <p class="mt-1 text-sm portal-muted">Active learners in your catalog</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Statistics</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-950">{{ analytics.summary.average_lessons_per_course }}</p>
                            <p class="mt-1 text-sm portal-muted">Average lessons per course</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Trend ratio</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-950">
                                {{ enrollmentTrendRatio }}%
                            </p>
                            <p class="mt-1 text-sm portal-muted">Monthly enrollments compared to yearly total</p>
                        </div>
                    </div>
                </article>
            </div>
        </template>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import StatCard from '@/src/components/shared/StatCard.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { portalApi } from '@/src/services/api';

const { t } = useI18n();
const { data: analytics, loading, error, execute } = useAsyncState(null);
const enrollmentTrendRatio = computed(() => {
    const monthly = Number(analytics.value?.summary?.monthly_enrollments ?? 0);
    const yearly = Number(analytics.value?.summary?.yearly_enrollments ?? 0);

    if (yearly <= 0) {
        return 0;
    }

    return Math.round((monthly / yearly) * 100);
});

onMounted(() => {
    execute(() => portalApi.getTutorAnalytics());
});
</script>
