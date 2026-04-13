<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <div class="flex shrink-0 items-center">
                                <Link :href="route($page.props.auth.user.role + '.dashboard')">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route($page.props.auth.user.role + '.dashboard')" :active="route().current($page.props.auth.user.role + '.dashboard')">
                                    Tableau de bord
                                </NavLink>

                                <NavLink v-if="$page.props.auth.user.role === 'etudiant'" :href="`${route('etudiant.dashboard')}#decouverte`" :active="false">
                                    Decouvrir des cours
                                </NavLink>

                                <NavLink v-if="$page.props.auth.user.role === 'tuteur'" :href="`${route('tuteur.dashboard')}#creation-cours`" :active="false">
                                    Creer un cours
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                                {{ $page.props.auth.user.nom }} 
                                                
                                                <span class="ml-2 px-2 py-0.5 text-xs bg-indigo-100 text-indigo-700 rounded capitalize">
                                                    {{ $page.props.auth.user.role }}
                                                </span>

                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Profil </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button"> Deconnexion </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <div
                v-if="$page.props.flash?.success || $page.props.flash?.error || $page.props.flash?.info"
                class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8"
            >
                <div
                    v-if="$page.props.flash?.success"
                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
                >
                    {{ $page.props.flash.success }}
                </div>

                <div
                    v-if="$page.props.flash?.error"
                    class="mt-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"
                >
                    {{ $page.props.flash.error }}
                </div>

                <div
                    v-if="$page.props.flash?.info"
                    class="mt-3 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800"
                >
                    {{ $page.props.flash.info }}
                </div>
            </div>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
