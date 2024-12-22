<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

// Path gambar harus diatur dalam bentuk relatif atau absolut
const backgroundUrl = '/assets/images/backgound_tracking.svg';
</script>

<template>
    <Head title="Welcome" />
    <div class="relative min-h-screen flex justify-end bg-cover bg-center"
         :style="{ backgroundImage: `url(${backgroundUrl})` }">
        <!-- <div class="absolute inset-0 bg-black/50"></div> -->
        <div class="relative z-10 text-center text-white space-y-4">
            <div class="flex justify-center space-x-4 p-4">
                <Link
                    v-if="canLogin"
                    :href="route('login')"
                    class="px-6 py-3 bg-black bg-opacity-20 lg:bg-opacity-0 text-lg hover:scale-105 animate-bounce hover:animate-none transition-transform rounded-lg font-semibold">
                    Login
                </Link>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="px-6 py-3 text-lg bg-black bg-opacity-20 lg:bg-opacity-0 hover:scale-110 rounded-lg font-semibold">
                    Register
                </Link>
            </div>
        </div>
    </div>
</template>
