<template>
    <div class="portal-page-grid">
        <ErrorState v-if="error" :message="error" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingCourse')" />

        <template v-else-if="course">
            <ErrorState v-if="actionError" :message="actionError" />

            <PageHeader
                :eyebrow="t('spa.coursePageEyebrow')"
                :title="course.title"
                :description="course.summary || t('routes.student.courseDetailsSubtitle')"
            >
                <template #actions>
                    <button
                        v-if="!course.is_enrolled"
                        type="button"
                        class="portal-button-primary disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="enrolling"
                        @click="enroll"
                    >
                        {{ enrolling ? t('spa.enrolling') : t('spa.enrollNow') }}
                    </button>
                    <RouterLink
                        v-if="firstLessonId"
                        :to="{ name: 'student-lesson', params: { id: firstLessonId } }"
                        class="portal-button-secondary"
                    >
                        {{ t('common.startFirstLesson') }}
                    </RouterLink>
                </template>
            </PageHeader>

            <div class="grid gap-5 md:grid-cols-3">
                <StatCard :label="t('common.lessons')" :value="course.lessons_count" />
                <StatCard :label="t('common.students')" :value="course.students_count" />
                <StatCard :label="t('common.track')" :value="course.track || t('spa.noTrack')" />
            </div>

            <div class="portal-page-grid-lg">
                <LessonList
                    :title="t('common.lessons')"
                    :eyebrow="t('common.course')"
                    :lessons="course.lessons"
                    lesson-route-name="student-lesson"
                />

                <aside class="space-y-5">
                    <CourseCard
                        :course="course"
                        :primary-to="firstLessonId ? { name: 'student-lesson', params: { id: firstLessonId } } : null"
                        :primary-label="t('common.continueLearning')"
                    />

                    <div v-if="statusMessage" class="portal-alert-success">
                        {{ statusMessage }}
                    </div>
                </aside>
            </div>
        </template>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import CourseCard from '@/src/components/courses/CourseCard.vue';
import LessonList from '@/src/components/lessons/LessonList.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import StatCard from '@/src/components/shared/StatCard.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { extractErrorMessage, portalApi } from '@/src/services/api';

const route = useRoute();
const enrolling = ref(false);
const statusMessage = ref('');
const actionError = ref('');
const { t } = useI18n();
const { data: course, loading, error, execute } = useAsyncState(null);

const firstLessonId = computed(() => course.value?.lessons?.[0]?.id ?? null);

async function loadCourse() {
    actionError.value = '';
    await execute(() => portalApi.getCourse(route.params.id));
}

async function enroll() {
    enrolling.value = true;
    statusMessage.value = '';
    actionError.value = '';

    try {
        const response = await portalApi.enrollInCourse(route.params.id);

        statusMessage.value = response.message;
        await loadCourse();
    } catch (requestError) {
        actionError.value = extractErrorMessage(requestError, t('spa.loadError'));
    } finally {
        enrolling.value = false;
    }
}

watch(
    () => route.params.id,
    () => {
        loadCourse();
    },
    { immediate: true },
);
</script>
