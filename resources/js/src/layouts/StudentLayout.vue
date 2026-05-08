<template>
    <div class="portal-shell">
        <div v-if="sidebarOpen" class="fixed inset-0 z-30 bg-slate-950/30 lg:hidden" @click="sidebarOpen = false" />

        <SidebarNav
            label="layouts.student.label"
            title="layouts.student.title"
            subtitle="layouts.student.subtitle"
            :open="sidebarOpen"
            :items="studentNavigation"
            theme="student"
            @close="sidebarOpen = false"
        />

        <main class="min-h-screen px-4 pb-8 pt-4 lg:ml-[20rem] lg:px-6">
            <TopBar
                role-label="layouts.student.roleLabel"
                :title="pageTitle"
                :subtitle="pageSubtitle"
                theme="student"
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
import { studentNavigation } from '@/src/config/navigation';

const route = useRoute();
const sidebarOpen = ref(false);

const pageTitle = computed(() => route.meta.titleKey ?? 'layouts.student.fallbackTitle');
const pageSubtitle = computed(() => route.meta.subtitleKey ?? 'layouts.student.fallbackSubtitle');

watch(
    () => route.fullPath,
    () => {
        sidebarOpen.value = false;
    },
);
</script>
