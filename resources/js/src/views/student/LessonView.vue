<template>
    <div class="portal-page-grid">
        <ErrorState v-if="error" :message="error" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingLesson')" />

        <template v-else-if="lesson">
            <PageHeader
                :eyebrow="t('spa.lessonPlayerEyebrow')"
                :title="lesson.title"
                :description="lesson.course?.title || t('routes.student.lessonSubtitle')"
            >
                <template #actions>
                    <RouterLink
                        v-if="lesson.course?.id"
                        :to="{ name: 'student-course-details', params: { id: lesson.course.id } }"
                        class="portal-button-secondary"
                    >
                        {{ t('spa.backToCourse') }}
                    </RouterLink>
                </template>
            </PageHeader>

            <LessonPlayer :lesson="lesson" />
        </template>
    </div>
</template>

<script setup>
import { watch } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import LessonPlayer from '@/src/components/lessons/LessonPlayer.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { portalApi } from '@/src/services/api';

const route = useRoute();
const { t } = useI18n();
const { data: lesson, loading, error, execute } = useAsyncState(null);

watch(
    () => route.params.id,
    () => {
        execute(() => portalApi.getLesson(route.params.id));
    },
    { immediate: true },
);
</script>
