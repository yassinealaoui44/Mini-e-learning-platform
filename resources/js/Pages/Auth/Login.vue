<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const { t } = useI18n();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.login.head')" />

        <div class="mb-8">
            <h2 class="font-['Archivo'] text-3xl font-semibold text-slate-950">{{ t('auth.login.title') }}</h2>
            <p class="mt-2 text-sm text-slate-600">{{ t('auth.login.subtitle') }}</p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" :value="t('auth.fields.email')" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="t('auth.fields.password')" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-slate-600">{{ t('auth.login.remember') }}</span>
                </label>
            </div>

            <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-slate-600 underline hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                >
                    {{ t('auth.login.forgotPassword') }}
                </Link>

                <div class="flex items-center justify-between gap-4 sm:ms-auto">
                    <p class="text-sm text-slate-600">
                        {{ t('auth.login.noAccount') }}
                        <Link :href="route('register')" class="font-semibold text-slate-900 underline">
                            {{ t('auth.login.register') }}
                        </Link>
                    </p>

                    <PrimaryButton
                        class="justify-center"
                        :loading="form.processing"
                    >
                        {{ t('auth.login.button') }}
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
