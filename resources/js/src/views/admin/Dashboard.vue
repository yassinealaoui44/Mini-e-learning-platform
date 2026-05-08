<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.adminOverviewEyebrow')"
            :title="t('spa.adminOverviewTitle')"
            :description="t('spa.adminOverviewDescription')"
        >
            <template #actions>
                <RouterLink
                    :to="{ name: 'admin-users' }"
                    class="portal-button-primary"
                >
                    {{ t('common.manage') }} {{ t('common.users').toLowerCase() }}
                </RouterLink>
                <RouterLink
                    :to="{ name: 'admin-courses' }"
                    class="portal-button-secondary"
                >
                    {{ t('common.manage') }} {{ t('common.courses').toLowerCase() }}
                </RouterLink>
            </template>
        </PageHeader>

        <ErrorState v-if="error" :message="error" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingDashboard')" />

        <template v-else-if="dashboard">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <StatCard :label="t('common.users')" :value="dashboard.stats.users" />
                <StatCard :label="t('common.courses')" :value="dashboard.stats.courses" />
                <StatCard :label="t('common.lessons')" :value="dashboard.stats.lessons" />
                <StatCard :label="t('spa.adminStatsEnrollments')" :value="dashboard.stats.enrollments" />
            </div>

            <div class="portal-page-grid-lg">
                <div class="space-y-6">
                    <section class="portal-card rounded-[28px] p-6">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ t('spa.roleBreakdown') }}</p>
                                <h3 class="portal-display mt-2 text-2xl font-semibold text-slate-950">{{ t('common.users') }}</h3>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-3">
                            <div
                                v-for="item in roleBreakdown"
                                :key="item.label"
                                class="rounded-2xl border border-slate-200 bg-white px-4 py-4"
                            >
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ item.label }}</p>
                                <p class="mt-3 text-3xl font-semibold text-slate-950">{{ item.value }}</p>
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ t('spa.recentPublishing') }}</p>
                                <h3 class="portal-display mt-2 text-2xl font-semibold text-slate-950">{{ t('spa.activeCatalog') }}</h3>
                            </div>
                            <RouterLink :to="{ name: 'admin-courses' }" class="text-sm font-semibold text-slate-900">{{ t('common.open') }}</RouterLink>
                        </div>

                        <div class="mt-5 grid gap-5 xl:grid-cols-2">
                            <CourseCard
                                v-for="course in dashboard.courses"
                                :key="course.id"
                                :course="course"
                                accent="admin"
                                :primary-to="{ name: 'admin-course-edit', params: { id: course.id } }"
                                :primary-label="t('common.edit')"
                                :secondary-to="{ name: 'admin-course-lessons', params: { id: course.id } }"
                                :secondary-label="t('common.lessons')"
                            />
                        </div>
                    </section>
                </div>

                <aside class="space-y-6">
                    <section class="portal-card rounded-[28px] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ t('common.users') }}</p>
                        <div class="mt-5 space-y-4">
                            <div
                                v-for="user in dashboard.users"
                                :key="user.id"
                                class="rounded-2xl border border-slate-200 bg-white px-4 py-4"
                            >
                                <p class="text-base font-semibold text-slate-950">{{ user.full_name }}</p>
                                <p class="mt-1 text-sm portal-muted">{{ user.email }}</p>
                                <p class="mt-3 text-xs font-semibold uppercase tracking-[0.16em] text-amber-700">
                                    {{ t(`options.roles.${user.primary_role}`) }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <section class="portal-card rounded-[28px] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ t('spa.newestLessons') }}</p>
                        <div class="mt-5 space-y-4">
                            <div
                                v-for="lesson in dashboard.recent_lessons"
                                :key="lesson.id"
                                class="rounded-2xl border border-slate-200 bg-white px-4 py-4"
                            >
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ lesson.type === 'video' ? t('common.video') : 'PDF' }}</p>
                                <p class="mt-2 text-base font-semibold text-slate-950">{{ lesson.title }}</p>
                                <p class="mt-1 text-sm portal-muted">{{ lesson.course?.title }}</p>
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
import { computed } from 'vue';
import CourseCard from '@/src/components/courses/CourseCard.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import StatCard from '@/src/components/shared/StatCard.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { portalApi } from '@/src/services/api';

const { t } = useI18n();
const { data: dashboard, loading, error, execute } = useAsyncState(null);
const roleBreakdown = computed(() =>
    dashboard.value
        ? [
              { label: t('spa.adminStatsAdmins'), value: dashboard.value.stats.admins },
              { label: t('spa.adminStatsTutors'), value: dashboard.value.stats.tutors },
              { label: t('spa.adminStatsStudents'), value: dashboard.value.stats.students },
          ]
        : [],
);

onMounted(() => {
    execute(() => portalApi.getAdminDashboard());
});
</script>
