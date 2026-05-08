<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.16),_transparent_26%),radial-gradient(circle_at_right_center,_rgba(16,185,129,0.12),_transparent_24%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] px-4 py-8"
    >
        <div class="mx-auto w-full max-w-5xl">
            <div class="flex flex-col items-center gap-6 text-center">
                <div class="max-w-2xl">
                    <Link href="/" class="inline-flex">
                        <BrandMark />
                    </Link>
                    <h1 class="mt-8 font-['Archivo'] text-4xl font-semibold text-slate-950">
                        {{ t('guest.title') }}
                    </h1>
                    <p class="mx-auto mt-4 max-w-xl text-base text-slate-600">
                        {{ t('guest.subtitle') }}
                    </p>
                </div>

                <LanguageToggle theme="sky" />
            </div>
        </div>

        <div
            class="mx-auto mt-10 w-full overflow-hidden rounded-[28px] border border-white/60 bg-white/90 px-6 py-10 shadow-[0_24px_60px_rgba(15,23,42,0.08)] backdrop-blur sm:max-w-xl"
        >
            <slot />
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import BrandMark from '@/shared/BrandMark.vue';
import LanguageToggle from '@/shared/LanguageToggle.vue';

const { t } = useI18n();
</script>