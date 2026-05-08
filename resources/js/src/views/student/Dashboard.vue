<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.studentOverviewEyebrow')"
            :title="t('spa.studentOverviewTitle')"
            :description="t('spa.studentOverviewDescription')"
        >
            <template #actions>
                <RouterLink
                    :to="{ name: 'student-courses' }"
                    class="portal-button-primary"
                >
                    {{ t('spa.browseCourses') }}
                </RouterLink>
                <RouterLink
                    :to="{ name: 'student-discover' }"
                    class="portal-button-secondary"
                >
                    {{ t('spa.discoverMore') }}
                </RouterLink>
            </template>
        </PageHeader>

        <ErrorState v-if="error" :message="error" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingDashboard')" />

        <template v-else-if="dashboard">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <StatCard :label="t('common.courses')" :value="dashboard.stats.enrolled_courses" />
                <StatCard :label="t('spa.recommended')" :value="dashboard.stats.recommended_courses" />
                <StatCard :label="t('common.lessons')" :value="dashboard.stats.total_lessons" />
                <StatCard :label="t('common.tutors')" :value="dashboard.stats.active_tutors" />
            </div>

            <div class="portal-page-grid-lg">
                <div class="space-y-6">
                    <section>
                        <h3 class="portal-display text-2xl font-semibold text-slate-950">{{ t('spa.continueLearningTitle') }}</h3>
                        <p class="mt-2 text-sm portal-muted">{{ t('spa.continueLearningDescription') }}</p>

                        <div class="mt-5 grid gap-5 xl:grid-cols-2">
                            <CourseCard
                                v-for="course in dashboard.enrolled_courses"
                                :key="course.id"
                                :course="course"
                                :primary-to="{ name: 'student-course-details', params: { id: course.id } }"
                                :primary-label="t('common.openCourse')"
                            />
                        </div>
                    </section>

                    <section class="portal-card rounded-[28px] p-6">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ t('spa.recentLessonsEyebrow') }}</p>
                                <h3 class="portal-display mt-2 text-2xl font-semibold text-slate-950">{{ t('spa.recentLessonsTitle') }}</h3>
                            </div>
                            <RouterLink
                                :to="{ name: 'student-progress' }"
                                class="text-sm font-semibold text-slate-700 transition hover:text-slate-950"
                            >
                                {{ t('spa.openProgress') }}
                            </RouterLink>
                        </div>

                        <div v-if="dashboard.recent_lessons.length" class="mt-5 space-y-3">
                            <RouterLink
                                v-for="lesson in dashboard.recent_lessons"
                                :key="lesson.id"
                                :to="{ name: 'student-lesson', params: { id: lesson.id } }"
                                class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-4 transition hover:border-slate-300"
                            >
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ lesson.type === 'video' ? t('common.video') : 'PDF' }}</p>
                                    <h4 class="mt-1 text-base font-semibold text-slate-950">{{ lesson.title }}</h4>
                                    <p class="mt-1 text-sm portal-muted">{{ lesson.course?.title }}</p>
                                </div>
                                <span class="text-sm font-semibold text-slate-700">{{ t('common.open') }}</span>
                            </RouterLink>
                        </div>

                        <EmptyState
                            v-else
                            :eyebrow="t('spa.noRecentLessonsEyebrow')"
                            :title="t('spa.noRecentLessonsTitle')"
                            :description="t('spa.noRecentLessonsDescription')"
                        />
                    </section>
                </div>

                <aside class="space-y-6">
                    <section class="portal-card rounded-[28px] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ t('spa.quickActions') }}</p>
                        <div class="mt-5 grid gap-3">
                            <RouterLink
                                :to="{ name: 'student-courses' }"
                                class="portal-button-primary"
                            >
                                {{ t('spa.browseEnrolledCourses') }}
                            </RouterLink>
                            <RouterLink
                                :to="{ name: 'student-discover' }"
                                class="portal-button-secondary"
                            >
                                {{ t('spa.findNewCourse') }}
                            </RouterLink>
                            <RouterLink
                                :to="{ name: 'student-profile' }"
                                class="portal-button-secondary"
                            >
                                {{ t('spa.updateMyProfile') }}
                            </RouterLink>
                        </div>
                    </section>

                    <section class="portal-card rounded-[28px] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ t('spa.recommended') }}</p>
                        <h3 class="portal-display mt-2 text-2xl font-semibold text-slate-950">{{ t('spa.suggestedNextPicks') }}</h3>

                        <div class="mt-5 space-y-4">
                            <div
                                v-for="course in dashboard.recommended_courses"
                                :key="course.id"
                                class="rounded-2xl border border-slate-200 bg-white px-4 py-4"
                            >
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-700">{{ course.track }}</p>
                                <h4 class="mt-2 text-base font-semibold text-slate-950">{{ course.title }}</h4>
                                <p class="mt-2 text-sm portal-muted">{{ course.tutor_name }}</p>
                                <RouterLink
                                    :to="{ name: 'student-course-details', params: { id: course.id } }"
                                    class="mt-4 inline-flex text-sm font-semibold text-slate-900"
                                >
                                    {{ t('common.viewDetails') }}
                                </RouterLink>
                            </div>
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
import EmptyState from '@/src/components/shared/EmptyState.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import StatCard from '@/src/components/shared/StatCard.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { portalApi } from '@/src/services/api';

const { t } = useI18n();
const { data: dashboard, loading, error, execute } = useAsyncState(null);

onMounted(() => {
    execute(() => portalApi.getStudentDashboard());
});
</script>
