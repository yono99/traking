<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const userUnit = computed(() => page.props.auth.user.unit || "Tidak ada unit");
const userRole = computed(() => page.props.auth.user.role || "Tidak ada role");
</script>

<template>
    <div class="mt-10 sm:mt-0">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        Informasi Unit dan Role
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Informasi detail mengenai unit dan role pengguna dalam
                        sistem.
                    </p>
                </div>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <div
                    class="py-5 bg-white dark:bg-gray-800 sm:p-6 shadow sm:rounded-lg"
                >
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <div class="space-y-2">
                                <!-- Unit Info -->
                                <div class="flex justify-item space-x-2">
                                    <label
                                        class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Unit :
                                    </label>
                                    <span
                                        class="text-sm text-gray-900 dark:text-gray-100"
                                    >
                                        {{ userUnit }}
                                    </span>
                                </div>

                                <!-- Role Info -->
                                <div class="flex justify-item space-x-2">
                                    <label
                                        class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Role :
                                    </label>
                                    <span
                                        class="text-sm text-gray-900 dark:text-gray-100"
                                    >
                                        {{ userRole }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
