<template>
    <div class="portal-shell">
        <div v-if="sidebarOpen" class="fixed inset-0 z-30 bg-slate-950/30 lg:hidden" @click="sidebarOpen = false" />

        <SidebarNav
            label="layouts.admin.label"
            title="layouts.admin.title"
            subtitle="layouts.admin.subtitle"
            :open="sidebarOpen"
            :items="adminNavigation"
            theme="admin"
            @close="sidebarOpen = false"
        />

        <main class="min-h-screen px-4 pb-8 pt-4 lg:ml-[20rem] lg:px-6">
            <TopBar
                role-label="layouts.admin.roleLabel"
                :title="pageTitle"
                :subtitle="pageSubtitle"
                theme="admin"
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
import { adminNavigation } from '@/src/config/navigation';

const route = useRoute();
const sidebarOpen = ref(false);

const pageTitle = computed(() => route.meta.titleKey ?? 'layouts.admin.fallbackTitle');
const pageSubtitle = computed(() => route.meta.subtitleKey ?? 'layouts.admin.fallbackSubtitle');

watch(
    () => route.fullPath,
    () => {
        sidebarOpen.value = false;
    },
);
</script>
