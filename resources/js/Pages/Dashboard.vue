<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Components/Welcome.vue";
import hitung from "@/Components/hitung.vue";
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Selamat Datang di Aplikasi SMART!
            </h2>
        </template>
        <hitung />
        <div class="flex flex-col items-center">
            <div class="w-full max-w-7xl px-4 py-2">
                <Welcome />
            </div>
        </div>
    </AppLayout>
</template>
