<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex">
        <nav class="w-64 bg-slate-900 text-white p-6 shadow-xl">
            <h1 class="text-xl font-bold border-b border-slate-700 pb-4 mb-6">EMSI E-Learn</h1>
            <ul class="space-y-2">
                <li><Link href="#" class="block p-3 rounded hover:bg-slate-800 transition">📊 Dashboard</Link></li>
                <li><Link href="#" class="block p-3 rounded hover:bg-slate-800 transition">📂 Mes Fichiers</Link></li>
            </ul>
            
            <div class="mt-auto pt-10">
                <Link href="/logout" method="post" as="button" class="w-full text-left p-3 text-red-400 hover:bg-red-900/20 rounded">
                    🚪 Déconnexion
                </Link>
            </div>
        </nav>

        <main class="flex-1 p-8">
            <slot /> </main>
    </div>
</template>