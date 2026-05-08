<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.studentsEyebrow')"
            :title="t('spa.studentsTitle')"
            :description="t('spa.studentsDescription')"
        />

        <ErrorState v-if="error" :message="error" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingUsers')" />

        <template v-else-if="payload">
            <div class="grid gap-4 md:grid-cols-3">
                <StatCard :label="t('common.students')" :value="payload.summary.students" />
                <StatCard :label="t('common.courses')" :value="payload.summary.active_courses" />
                <StatCard :label="t('common.statistics')" :value="payload.summary.average_courses_per_student" />
            </div>

            <div class="portal-card overflow-hidden rounded-[28px]">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="portal-display text-2xl font-semibold text-slate-950">{{ t('spa.enrollmentRoster') }}</h3>
                </div>

                <div v-if="payload.students.length" class="divide-y divide-slate-200">
                    <div
                        v-for="student in payload.students"
                        :key="student.id"
                        class="grid gap-4 px-6 py-5 lg:grid-cols-[minmax(0,1fr)_10rem_12rem]"
                    >
                        <div class="min-w-0">
                            <p class="text-base font-semibold text-slate-950">{{ student.full_name }}</p>
                            <p class="mt-1 text-sm portal-muted">{{ student.email }}</p>
                            <p class="mt-3 text-sm text-slate-700">
                                {{ t('common.track') }}: {{ student.tracks.join(', ') || t('spa.noTrack') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ t('common.courses') }}</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950">{{ student.enrolled_courses_count }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ t('common.recent') }}</p>
                            <p class="mt-2 text-sm font-semibold text-slate-950">{{ formatPortalDate(student.latest_enrollment_at, locale) || student.latest_enrollment || t('spa.recently') }}</p>
                        </div>
                    </div>
                </div>

                <EmptyState
                    v-else
                    :eyebrow="t('common.noData')"
                    :title="t('spa.noStudentsTitle')"
                    :description="t('spa.noStudentsDescription')"
                />
            </div>
        </template>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import EmptyState from '@/src/components/shared/EmptyState.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import StatCard from '@/src/components/shared/StatCard.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { formatPortalDate } from '@/src/utils/formatters';
import { portalApi } from '@/src/services/api';

const { locale, t } = useI18n();
const { data: payload, loading, error, execute } = useAsyncState(null);

onMounted(() => {
    execute(() => portalApi.getTutorStudents());
});
</script>
