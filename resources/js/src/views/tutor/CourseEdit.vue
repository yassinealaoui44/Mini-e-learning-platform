<template>
    <div class="portal-page-grid">
        <ErrorState v-if="submitError" :message="submitError" />
        <ErrorState v-if="error" :message="error" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingCourse')" />

        <template v-else-if="course">
            <PageHeader
                :eyebrow="t('spa.editCourseEyebrow')"
                :title="`${t('common.edit')} ${course.title}`"
                :description="t('routes.tutor.editCourseSubtitle')"
            >
                <template #actions>
                    <RouterLink
                        :to="{ name: 'tutor-course-lessons', params: { id: course.id } }"
                        class="portal-button-secondary"
                    >
                        {{ t('spa.manageLessons') }}
                    </RouterLink>
                </template>
            </PageHeader>

            <div v-if="message" class="portal-alert-success">
                {{ message }}
            </div>

            <CourseForm
                :initial-values="{ title: course.title, track: course.track }"
                :current-cover-image-url="course.cover_image_url"
                :submit-label="t('common.save')"
                :busy="saving"
                :errors="serverErrors"
                @submit="submit"
            />
        </template>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import CourseForm from '@/src/components/courses/CourseForm.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { extractErrorMessage, portalApi } from '@/src/services/api';

const route = useRoute();
const { t } = useI18n();
const saving = ref(false);
const message = ref('');
const serverErrors = ref({});
const submitError = ref('');
const { data: course, loading, error, execute } = useAsyncState(null);

async function loadCourse() {
    message.value = '';
    await execute(() => portalApi.getCourse(route.params.id));
}

async function submit(values) {
    saving.value = true;
    message.value = '';
    serverErrors.value = {};
    submitError.value = '';

    try {
        const payload = await portalApi.updateCourse(route.params.id, {
            nom: values.title,
            filiere: values.track,
            cover_image: values.cover_image,
        });

        course.value = payload.course;
        message.value = payload.message;
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
