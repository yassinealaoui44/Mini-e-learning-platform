<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.courseDiscoveryEyebrow')"
            :title="t('spa.courseDiscoveryTitle')"
            :description="t('spa.courseDiscoveryDescription')"
        />

        <ErrorState v-if="actionError" :message="actionError" />
        <ErrorState v-if="error" :message="error" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingCourses')" />

        <div v-else-if="payload.courses.length" class="grid gap-5 xl:grid-cols-2">
            <CourseCard
                v-for="course in payload.courses"
                :key="course.id"
                :course="course"
                :primary-to="{ name: 'student-course-details', params: { id: course.id } }"
                :primary-label="t('common.viewSyllabus')"
            >
                <template #actions>
                    <button
                        type="button"
                        class="portal-button-primary disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="pendingCourseId === course.id"
                        @click="enroll(course.id)"
                    >
                        {{ pendingCourseId === course.id ? t('spa.enrolling') : t('spa.enroll') }}
                    </button>
                    <RouterLink
                        :to="{ name: 'student-course-details', params: { id: course.id } }"
                        class="portal-button-secondary"
                    >
                        {{ t('common.viewSyllabus') }}
                    </RouterLink>
                </template>
            </CourseCard>
        </div>

        <EmptyState
            v-else
            :eyebrow="t('common.noData')"
            :title="t('spa.noDiscoverTitle')"
            :description="t('spa.noDiscoverDescription')"
        />

        <div v-if="message" class="portal-alert-success">
            {{ message }}
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';
import CourseCard from '@/src/components/courses/CourseCard.vue';
import EmptyState from '@/src/components/shared/EmptyState.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { extractErrorMessage, portalApi } from '@/src/services/api';

const pendingCourseId = ref(null);
const message = ref('');
const actionError = ref('');
const { t } = useI18n();
const { data: payload, loading, error, execute } = useAsyncState({ courses: [] });

async function loadCourses() {
    await execute(() => portalApi.getCourses({ scope: 'discover' }));
}

async function enroll(courseId) {
    pendingCourseId.value = courseId;
    message.value = '';
    actionError.value = '';

    try {
        const response = await portalApi.enrollInCourse(courseId);
        message.value = response.message;

        await loadCourses();
    } catch (requestError) {
        actionError.value = extractErrorMessage(requestError, t('spa.loadError'));
    } finally {
        pendingCourseId.value = null;
    }
}

onMounted(() => {
    loadCourses();
});
</script>
