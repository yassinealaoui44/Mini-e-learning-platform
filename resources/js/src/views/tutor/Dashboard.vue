<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.tutorOverviewEyebrow')"
            :title="t('spa.tutorOverviewTitle')"
            :description="t('spa.tutorOverviewDescription')"
        >
            <template #actions>
                <RouterLink
                    :to="{ name: 'tutor-course-create' }"
                    class="portal-button-primary"
                >
                    {{ t('spa.createCourseButton') }}
                </RouterLink>
                <RouterLink
                    :to="{ name: 'tutor-analytics' }"
                    class="portal-button-secondary"
                >
                    {{ t('common.openAnalytics') }}
                </RouterLink>
            </template>
        </PageHeader>

        <ErrorState v-if="error" :message="error" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingDashboard')" />

        <template v-else-if="dashboard">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <StatCard :label="t('common.courses')" :value="dashboard.stats.courses" />
                <StatCard :label="t('common.lessons')" :value="dashboard.stats.lessons" />
                <StatCard :label="t('common.students')" :value="dashboard.stats.students" />
                <StatCard :label="t('common.track')" :value="dashboard.stats.tracks" />
            </div>

            <div class="portal-page-grid-lg">
                <div class="space-y-6">
                    <section>
                        <h3 class="portal-display text-2xl font-semibold text-slate-950">{{ t('spa.activeCatalog') }}</h3>
                        <p class="mt-2 text-sm portal-muted">{{ t('spa.activeCatalogDescription') }}</p>

                        <div class="mt-5 grid gap-5 xl:grid-cols-2">
                            <CourseCard
                                v-for="course in dashboard.courses"
                                :key="course.id"
                                :course="course"
                                accent="tutor"
                                :primary-to="{ name: 'tutor-course-edit', params: { id: course.id } }"
                                :primary-label="t('common.edit')"
                                :secondary-to="{ name: 'tutor-course-lessons', params: { id: course.id } }"
                                :secondary-label="t('spa.manageLessons')"
                            />
                        </div>
                    </section>

                    <LessonList
                        :eyebrow="t('spa.recentPublishing')"
                        :title="t('spa.newestLessons')"
                        :lessons="dashboard.recent_lessons"
                        lesson-route-name="tutor-lesson-viewer"
                    />
                </div>

                <aside class="space-y-6">
                    <section class="portal-card rounded-[28px] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ t('spa.quickActions') }}</p>
                        <div class="mt-5 grid gap-3">
                            <RouterLink
                                :to="{ name: 'tutor-course-create' }"
                                class="portal-button-primary"
                            >
                                {{ t('spa.createCourseButton') }}
                            </RouterLink>
                            <RouterLink
                                :to="{ name: 'tutor-courses' }"
                                class="portal-button-secondary"
                            >
                                {{ t('common.manage') }} {{ t('common.courses').toLowerCase() }}
                            </RouterLink>
                            <RouterLink
                                :to="{ name: 'tutor-students' }"
                                class="portal-button-secondary"
                            >
                                {{ t('common.students') }}
                            </RouterLink>
                        </div>
                    </section>
                </aside>
            </div>
        </template>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';
import CourseCard from '@/src/components/courses/CourseCard.vue';
import LessonList from '@/src/components/lessons/LessonList.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import StatCard from '@/src/components/shared/StatCard.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { portalApi } from '@/src/services/api';

const { t } = useI18n();
const { data: dashboard, loading, error, execute } = useAsyncState(null);

onMounted(() => {
    execute(() => portalApi.getTutorDashboard());
});
</script>
