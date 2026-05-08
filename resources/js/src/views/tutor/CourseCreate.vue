<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.createCourseEyebrow')"
            :title="t('spa.createCourseTitle')"
            :description="t('spa.createCourseDescription')"
        />

        <ErrorState v-if="submitError" :message="submitError" />

        <div v-if="message" class="portal-alert-success">
            {{ message }}
        </div>

        <CourseForm
            :submit-label="t('spa.createCourseButton')"
            :busy="saving"
            :errors="serverErrors"
            @submit="submit"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import CourseForm from '@/src/components/courses/CourseForm.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import { extractErrorMessage, portalApi } from '@/src/services/api';

const router = useRouter();
const { t } = useI18n();
const saving = ref(false);
const message = ref('');
const serverErrors = ref({});
const submitError = ref('');

async function submit(values) {
    saving.value = true;
    message.value = '';
    serverErrors.value = {};
    submitError.value = '';

    try {
        const payload = await portalApi.createCourse({
            nom: values.title,
            filiere: values.track,
            cover_image: values.cover_image,
        });

        message.value = payload.message;
        await router.push({ name: 'tutor-course-edit', params: { id: payload.course.id } });
    } catch (requestError) {
        serverErrors.value = requestError.response?.data?.errors ?? {};
        submitError.value = extractErrorMessage(requestError);
    } finally {
        saving.value = false;
    }
}
</script>
