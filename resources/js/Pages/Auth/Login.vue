<script setup>
import { useForm, Head } from '@inertiajs/vue3';

// 1. Initialize the Inertia form helper
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// 2. The submit function
const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'), // Clear password on failure
    });
};
</script>

<template>
    <Head title="Connexion" />

    <div class="min-h-screen flex items-center justify-center bg-slate-100 px-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
            
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-blue-600">E-Learning</h1>
                <p class="text-slate-500 mt-2">Connectez-vous pour accéder à vos cours</p>
            </div>

            <div v-if="form.errors.email" class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
                {{ form.errors.email }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Adresse Email</label>
                    <input 
                        v-model="form.email" 
                        type="email" 
                        required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        :class="{'border-red-500': form.errors.email}"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Mot de passe</label>
                    <input 
                        v-model="form.password" 
                        type="password" 
                        required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    >
                </div>

                <div class="flex items-center">
                    <input v-model="form.remember" type="checkbox" id="remember" class="rounded text-blue-600 shadow-sm">
                    <label for="remember" class="ml-2 text-sm text-slate-600">Se souvenir de moi</label>
                </div>

                <button 
                    type="submit" 
                    :disabled="form.processing"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-200 shadow-lg active:transform active:scale-95 disabled:opacity-50"
                >
                    <span v-if="form.processing">Connexion en cours...</span>
                    <span v-else>Se connecter</span>
                </button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-8">
                Besoin d'aide ? Contactez l'administration de l'EMSI.
            </p>
        </div>
    </div>
</template>