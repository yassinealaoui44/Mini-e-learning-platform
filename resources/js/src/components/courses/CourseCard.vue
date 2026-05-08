<template>
    <article class="portal-card overflow-hidden rounded-[28px]">
        <div :class="coverClass" class="relative h-48 overflow-hidden px-5 py-5 text-white">
            <img
                :src="thumbnailUrl"
                :alt="course.title"
                class="absolute inset-0 h-full w-full object-cover"
                @error="imageFailed = true"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent" />

            <div class="relative flex h-full flex-col justify-between">
                <div class="flex items-start justify-between gap-3">
                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] backdrop-blur">
                        {{ course.track || t('spa.noTrack') }}
                    </span>
                    <span class="rounded-full bg-slate-950/30 px-3 py-1 text-xs font-medium backdrop-blur">
                        {{ course.lessons_count }} {{ t('common.lessons').toLowerCase() }}
                    </span>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.16em] text-white/75">
                        {{ course.tutor_name || t('options.roles.tutor') }}
                    </p>
                    <h3 class="portal-display mt-2 text-2xl font-semibold leading-tight">
                        {{ course.title }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="space-y-5 px-5 py-5">
            <p class="text-sm portal-muted">
                {{ course.summary || defaultSummary }}
            </p>

            <dl class="grid grid-cols-2 gap-3 text-sm text-slate-700">
                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                    <dt class="text-xs uppercase tracking-[0.16em] text-slate-500">{{ t('common.students') }}</dt>
                    <dd class="mt-2 text-lg font-semibold text-slate-950">{{ course.students_count }}</dd>
                </div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                    <dt class="text-xs uppercase tracking-[0.16em] text-slate-500">{{ t('common.published') }}</dt>
                    <dd class="mt-2 text-lg font-semibold text-slate-950">{{ publishedAt }}</dd>
                </div>
            </dl>

            <div v-if="$slots.actions || primaryTo || secondaryTo" class="flex flex-wrap gap-3">
                <slot name="actions">
                    <RouterLink
                        v-if="primaryTo"
                        :to="primaryTo"
                        class="portal-button-primary"
                    >
                        {{ primaryLabel }}
                    </RouterLink>

                    <RouterLink
                        v-if="secondaryTo"
                        :to="secondaryTo"
                        class="portal-button-secondary"
                    >
                        {{ secondaryLabel }}
                    </RouterLink>
                </slot>
            </div>
        </div>
    </article>
</template>

<script setup>
import { computed, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { formatPortalDate } from '@/src/utils/formatters';

const props = defineProps({
    course: {
        type: Object,
        required: true,
    },
    primaryTo: {
        type: [Object, String],
        default: null,
    },
    primaryLabel: {
        type: String,
        default: '',
    },
    secondaryTo: {
        type: [Object, String],
        default: null,
    },
    secondaryLabel: {
        type: String,
        default: '',
    },
    accent: {
        type: String,
        default: 'student',
    },
});

const { locale, t } = useI18n();
const imageFailed = ref(false);

const coverClass = computed(() =>
    props.accent === 'tutor'
        ? 'bg-gradient-to-br from-emerald-600 via-teal-500 to-cyan-500'
        : props.accent === 'admin'
          ? 'bg-gradient-to-br from-amber-600 via-orange-500 to-rose-400'
        : 'bg-gradient-to-br from-sky-600 via-cyan-500 to-teal-400',
);

const defaultSummary = computed(() => t('spa.courseCardSummary'));

const thumbnailUrl = computed(() => {
    if (imageFailed.value) {
        return '/course-cover-placeholder.svg';
    }

    const source = props.course.cover_image_url ?? props.course.thumbnail_url ?? props.course.cover_image ?? props.course.thumbnail ?? '';

    if (!source) {
        return '/course-cover-placeholder.svg';
    }

    if (source.startsWith('http://') || source.startsWith('https://') || source.startsWith('/')) {
        return source;
    }

    return `/storage/${source}`;
});

const publishedAt = computed(
    () => formatPortalDate(props.course.created_at, locale.value) || props.course.created_at_label || t('spa.recently'),
);
</script>
