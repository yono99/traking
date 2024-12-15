 <script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
 import dateRange from '@/Components/DateRange.vue';

import Hitung_berkas_alihmedia_rutin from "@/Components/Hitung_berkas_alihmedia_rutin.vue";

</script>

<template>
    <AppLayout title="Dashboard">
        
        <Hitung_berkas_alihmedia_rutin/><dateRange/>
        <div class="flex flex-col items-center">
            <div class="w-full max-w-7xl px-4 py-2">
                <Welcome />
            </div>
        </div>
    </AppLayout>
</template>
