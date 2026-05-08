<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.createCourseEyebrow')"
            :title="t('spa.createCourseTitle')"
            :description="t('spa.addCourseForPlatform')"
        />

        <ErrorState v-if="error" :message="error" />
        <ErrorState v-if="submitError" :message="submitError" />

        <div v-if="message" class="portal-alert-success">
            {{ message }}
        </div>

        <LoadingState v-if="loading" :label="t('spa.loadingUsers')" />

        <CourseForm
            v-else
            :submit-label="t('spa.createCourseButton')"
            :busy="saving"
            :errors="serverErrors"
            :show-owner="true"
            :owner-options="ownerOptions"
            @submit="submit"
        />
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import CourseForm from '@/src/components/courses/CourseForm.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { extractErrorMessage, portalApi } from '@/src/services/api';

const router = useRouter();
const { t } = useI18n();
const saving = ref(false);
const message = ref('');
const submitError = ref('');
const serverErrors = ref({});
const { data: usersPayload, loading, error, execute } = useAsyncState({ users: [] });

const ownerOptions = computed(() =>
    (usersPayload.value.users ?? [])
        .filter((user) => user.primary_role === 'tutor')
        .map((user) => ({
            id: user.id,
            label: `${user.full_name} (${user.email})`,
        })),
);

async function submit(values) {
    saving.value = true;
    message.value = '';
    submitError.value = '';
    serverErrors.value = {};

    try {
        const response = await portalApi.createCourse({
            nom: values.title,
            filiere: values.track,
            cover_image: values.cover_image,
            id_tuteur: values.ownerId,
        });

        message.value = response.message;
        await router.push({ name: 'admin-course-edit', params: { id: response.course.id } });
    } catch (requestError) {
        serverErrors.value = requestError.response?.data?.errors ?? {};
        submitError.value = extractErrorMessage(requestError);
    } finally {
        saving.value = false;
    }
}

onMounted(() => {
    execute(() => portalApi.getUsers());
});
</script>
