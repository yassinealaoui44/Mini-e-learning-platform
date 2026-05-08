<template>
    <div class="portal-shell">
        <div v-if="sidebarOpen" class="fixed inset-0 z-30 bg-slate-950/30 lg:hidden" @click="sidebarOpen = false" />

        <SidebarNav
            label="layouts.tutor.label"
            title="layouts.tutor.title"
            subtitle="layouts.tutor.subtitle"
            :open="sidebarOpen"
            :items="tutorNavigation"
            theme="tutor"
            @close="sidebarOpen = false"
        />

        <main class="min-h-screen px-4 pb-8 pt-4 lg:ml-[20rem] lg:px-6">
            <TopBar
                role-label="layouts.tutor.roleLabel"
                :title="pageTitle"
                :subtitle="pageSubtitle"
                theme="tutor"
                @toggle-sidebar="sidebarOpen = true"
            />

            <section class="mt-6">
                <RouterView />
            </section>
        </main>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { RouterView, useRoute } from 'vue-router';
import SidebarNav from '@/src/components/shared/SidebarNav.vue';
import TopBar from '@/src/components/shared/TopBar.vue';
import { tutorNavigation } from '@/src/config/navigation';

const route = useRoute();
const sidebarOpen = ref(false);

const pageTitle = computed(() => route.meta.titleKey ?? 'layouts.tutor.fallbackTitle');
const pageSubtitle = computed(() => route.meta.subtitleKey ?? 'layouts.tutor.fallbackSubtitle');

watch(
    () => route.fullPath,
    () => {
        sidebarOpen.value = false;
    },
);
</script>
