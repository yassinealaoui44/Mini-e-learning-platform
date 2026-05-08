<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.studentCoursesEyebrow')"
            :title="t('spa.studentCoursesTitle')"
            :description="t('spa.studentCoursesDescription')"
        >
            <template #actions>
                <RouterLink
                    :to="{ name: 'student-discover' }"
                    class="portal-button-secondary"
                >
                    {{ t('spa.findNewCourse') }}
                </RouterLink>
            </template>
        </PageHeader>

        <div class="portal-card rounded-[28px] p-5">
            <label class="block space-y-2 text-sm font-medium text-slate-700">
                <span>{{ t('spa.searchCoursesStudent') }}</span>
                <input
                    v-model="search"
                    type="search"
                    :placeholder="t('spa.searchCoursesPlaceholderStudent')"
                    class="portal-input"
                >
            </label>
        </div>

        <ErrorState v-if="error" :message="error" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingCourses')" />

        <div v-else-if="filteredCourses.length" class="grid gap-5 xl:grid-cols-2">
            <CourseCard
                v-for="course in filteredCourses"
                :key="course.id"
                :course="course"
                :primary-to="{ name: 'student-course-details', params: { id: course.id } }"
                :primary-label="t('common.openCourse')"
            />
        </div>

        <EmptyState
            v-else
            :eyebrow="t('spa.noCoursesEyebrow')"
            :title="t('spa.noCoursesTitle')"
            :description="t('spa.noCoursesDescription')"
        >
            <template #actions>
                <RouterLink
                    :to="{ name: 'student-discover' }"
                    class="portal-button-primary"
                >
                    {{ t('spa.findNewCourse') }}
                </RouterLink>
            </template>
        </EmptyState>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';
import CourseCard from '@/src/components/courses/CourseCard.vue';
import EmptyState from '@/src/components/shared/EmptyState.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { portalApi } from '@/src/services/api';

const search = ref('');
const { t } = useI18n();
const { data: payload, loading, error, execute } = useAsyncState({ courses: [] });

const filteredCourses = computed(() => {
    const term = search.value.trim().toLowerCase();

    if (!term) {
        return payload.value.courses ?? [];
    }

    return (payload.value.courses ?? []).filter((course) =>
        [course.title, course.track, course.tutor_name]
            .filter(Boolean)
            .some((value) => value.toLowerCase().includes(term)),
    );
});

onMounted(() => {
    execute(() => portalApi.getCourses({ scope: 'enrolled' }));
});
</script>
