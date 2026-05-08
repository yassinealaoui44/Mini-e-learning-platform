<template>
    <div class="portal-page-grid">
        <ErrorState v-if="error" :message="error" />
        <ErrorState v-if="submitError" :message="submitError" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingLessons')" />

        <template v-else-if="course">
            <PageHeader
                :eyebrow="t('spa.lessonManagementEyebrow')"
                :title="`${t('common.lessons')} - ${course.title}`"
                :description="t('routes.admin.courseLessonsSubtitle')"
            >
                <template #actions>
                    <RouterLink
                        :to="{ name: 'admin-course-edit', params: { id: course.id } }"
                        class="portal-button-secondary"
                    >
                        {{ t('common.edit') }}
                    </RouterLink>
                </template>
            </PageHeader>

            <div class="grid gap-4 md:grid-cols-3">
                <StatCard :label="t('common.lessons')" :value="course.lessons_count" />
                <StatCard :label="t('common.students')" :value="course.students_count" />
                <StatCard :label="t('options.roles.tutor')" :value="course.owner?.name || t('common.notAssigned')" />
            </div>

            <div v-if="message" class="portal-alert-success">
                {{ message }}
            </div>

            <div class="portal-page-grid-lg">
                <LessonList
                    :eyebrow="t('spa.recentPublishing')"
                    :title="t('common.lessons')"
                    :lessons="course.lessons"
                    lesson-route-name="admin-lesson-viewer"
                />

                <LessonUploadForm
                    :busy="saving"
                    :errors="serverErrors"
                    @submit="submit"
                />
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import LessonList from '@/src/components/lessons/LessonList.vue';
import LessonUploadForm from '@/src/components/lessons/LessonUploadForm.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import StatCard from '@/src/components/shared/StatCard.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { extractErrorMessage, portalApi } from '@/src/services/api';

const route = useRoute();
const { t } = useI18n();
const saving = ref(false);
const message = ref('');
const submitError = ref('');
const serverErrors = ref({});
const { data: course, loading, error, execute } = useAsyncState(null);

async function loadCourse() {
    await execute(() => portalApi.getCourse(route.params.id));
}

async function submit(values) {
    saving.value = true;
    message.value = '';
    submitError.value = '';
    serverErrors.value = {};

    try {
        const response = await portalApi.createLessonForCourse(route.params.id, {
            titre: values.title,
            type: values.type,
            file: values.file,
        });

        message.value = response.message;
        values.reset();
        await loadCourse();
    } catch (requestError) {
        serverErrors.value = requestError.response?.data?.errors ?? {};
        submitError.value = extractErrorMessage(requestError);
    } finally {
        saving.value = false;
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
