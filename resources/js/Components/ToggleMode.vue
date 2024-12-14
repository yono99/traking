<template>
    <div class="fixed bottom-10 right-10 z-50">
        <button @click="toggleDarkMode"
            class="flex items-center gap-2 px-4 py-4 rounded-md bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
            <template v-if="isDarkMode">
                <!-- Ikon Matahari (Light Mode) -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3v1m0 16v1m8-8h1M4 12H3m15.364-7.364l.707.707m-12.02 12.02l-.707.707m12.02-12.02l.707-.707m-12.02 12.02l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </template>
            <template v-else>
                <!-- Ikon Bulan (Dark Mode) -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.354 15.354A9 9 0 118.646 3.646 7 7 0 0020.354 15.354z" />
                </svg>
            </template>
        </button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isDarkMode: false,
        };
    },
    mounted() {
        this.loadTheme();
    },
    methods: {
        toggleDarkMode() {
            this.isDarkMode = !this.isDarkMode;
            this.applyTheme();
        },
        loadTheme() {
            const theme = localStorage.getItem('theme') || 'light';
            this.isDarkMode = theme === 'dark';
            this.applyTheme();
        },
        applyTheme() {
            const root = document.documentElement;
            if (this.isDarkMode) {
                root.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                root.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        },
    },
};
</script>
