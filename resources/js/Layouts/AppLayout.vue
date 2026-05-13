<script setup>
import { ref, computed } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ToggleMode from "@/Components/ToggleMode.vue";
import Footer from "@/Components/Footer.vue";
import NotificationBell from "@/Components/NotificationBell.vue";
defineProps({
    title: String,
});

const page = usePage();
const sidebarOpen = ref(true);
const mobileOpen = ref(false);
const userMenuOpen = ref(false);

// Directive: tutup menu saat klik di luar
const vClickOutside = {
    mounted(el, binding) {
        el._clickOutside = (e) => {
            if (!el.contains(e.target)) binding.value(e);
        };
        document.addEventListener("mousedown", el._clickOutside);
    },
    unmounted(el) {
        document.removeEventListener("mousedown", el._clickOutside);
    },
};

const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => user.value?.role === "admin");
const userUnit = computed(() => user.value?.unit);

const unitList = [
    "loket", "verifikator", "pengukuran", "bensus", "pelaksana",
    "pelaksana_bn", "pelaksana_ph", "pelaksana_roya", "pelaksana_ph_ruko",
    "pelaksana_sk", "bukutanah", "sps", "QC", "pengesahan", "paraf",
    "TTE_PRODUK_LAYANAN", "LOKET_PENYERAHAN",
];

const unitListNoLoket = unitList.filter((u) => u !== "loket");

const navItems = computed(() => [
    {
        label: "Dashboard",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>`,
        href: route("dashboard"),
        active: route().current("dashboard"),
        show: true,
    },
    {
        label: "Management Akun",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>`,
        href: route("management.akun"),
        active: route().current("management.akun"),
        show: isAdmin.value,
    },
    {
        label: "Berkas",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>`,
        href: route("berkas.index"),
        active: route().current("berkas.index"),
        show: userUnit.value === "loket" || isAdmin.value,
    },
    {
        label: "Input Data",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>`,
        href: route("genggam.berkas"),
        active: route().current("genggam.berkas"),
        show: userUnit.value === "loket",
    },
    {
        label: "Berkas Pending",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
        href: route("tanya-smart.index"),
        active: route().current("tanya-smart.index"),
        show: unitList.includes(userUnit.value),
    },
    {
        label: "Inventory",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>`,
        href: route("inventory.index"),
        active: route().current("inventory.index"),
        show: unitList.includes(userUnit.value),
    },
    {
        label: "Laporan",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>`,
        href: isAdmin.value ? route("laporan.index") : route("laporan-unit.index"),
        active: route().current("laporan.index") || route().current("laporan-unit.index"),
        show: isAdmin.value || unitListNoLoket.includes(userUnit.value),
    },
    {
        label: "WA Gateway",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>`,
        href: route("wa-gateway.index"),
        active: route().current("wa-gateway.index"),
        show: isAdmin.value,
    },
]);

const logout = () => router.post(route("logout"));

const smartLogo = "/assets/images/favicon2.svg";
</script>

<template>
    <div class="flex min-h-screen bg-gray-50 dark:bg-gray-950 font-sans">
        <Head :title="title" />
        <Banner />

        <!-- ── OVERLAY mobile ── -->
        <Transition name="fade">
            <div
                v-if="mobileOpen"
                class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm sm:hidden"
                @click="mobileOpen = false"
            />
        </Transition>

        <!-- ══════════════════ SIDEBAR ══════════════════ -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-40 flex flex-col bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 transition-all duration-300 ease-in-out',
                sidebarOpen ? 'w-64' : 'w-[68px]',
                mobileOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0',
            ]"
        >
            <!-- Brand -->
            <div class="flex items-center h-16 px-4 border-b border-gray-100 dark:border-gray-800 shrink-0 gap-3 overflow-hidden">
                <Link :href="route('dashboard')" class="shrink-0">
                    <div
                        class="w-9 h-9 rounded-xl bg-cover bg-center ring-2 ring-blue-500/20 shadow-md shadow-blue-500/10"
                        :style="{ backgroundImage: `url(${smartLogo})` }"
                    />
                </Link>
                <Transition name="slide-fade">
                    <div v-if="sidebarOpen" class="overflow-hidden whitespace-nowrap">
                        <p class="text-sm font-bold text-gray-800 dark:text-white tracking-wide leading-tight">SMART</p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest">Sistem Manajemen</p>
                    </div>
                </Transition>
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4 px-2 space-y-0.5">
                <template v-for="item in navItems" :key="item.label">
                    <Link
                        v-if="item.show"
                        :href="item.href"
                        :class="[
                            'group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150',
                            item.active
                                ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400'
                                : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200',
                        ]"
                    >
                        <!-- Active indicator bar -->
                        <span
                            v-if="item.active"
                            class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-blue-500"
                        />

                        <!-- Icon -->
                        <svg
                            :class="[
                                'shrink-0 w-5 h-5 transition-colors',
                                item.active ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300',
                            ]"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            v-html="item.icon"
                        />

                        <!-- Label -->
                        <Transition name="slide-fade">
                            <span v-if="sidebarOpen" class="truncate whitespace-nowrap">{{ item.label }}</span>
                        </Transition>

                        <!-- Tooltip when collapsed -->
                        <div
                            v-if="!sidebarOpen"
                            class="absolute left-full ml-3 px-2.5 py-1.5 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-lg z-50"
                        >
                            {{ item.label }}
                            <span class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-gray-900 dark:border-r-gray-700" />
                        </div>
                    </Link>
                </template>
            </nav>

            <!-- Bottom: Dark mode toggle + user -->
            <div class="shrink-0 border-t border-gray-100 dark:border-gray-800 p-3 space-y-1">
                <!-- Toggle mode -->
                <div :class="['flex items-center gap-3 px-3 py-2 rounded-xl', sidebarOpen ? '' : 'justify-center']">
                    <ToggleMode />
                    <Transition name="slide-fade">
                        <span v-if="sidebarOpen" class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">Tema</span>
                    </Transition>
                </div>

                <!-- User popup (custom, opens upward) -->
                <div class="relative">
                    <button
                        @click="userMenuOpen = !userMenuOpen"
                        :class="[
                            'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-left group',
                            sidebarOpen ? '' : 'justify-center',
                            userMenuOpen ? 'bg-gray-50 dark:bg-gray-800' : '',
                        ]"
                    >
                        <img
                            v-if="$page.props.jetstream?.managesProfilePhotos"
                            class="h-8 w-8 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700 shrink-0"
                            :src="user?.profile_photo_url"
                            :alt="user?.name"
                        />
                        <div v-else class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {{ user?.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <Transition name="slide-fade">
                            <div v-if="sidebarOpen" class="flex-1 overflow-hidden">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 truncate leading-tight">{{ user?.name }}</p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 truncate uppercase tracking-wider">{{ user?.unit || user?.role }}</p>
                            </div>
                        </Transition>
                        <Transition name="slide-fade">
                            <svg
                                v-if="sidebarOpen"
                                :class="['w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200', userMenuOpen ? 'rotate-180' : '']"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </Transition>
                    </button>

                    <!-- Popup menu -->
                    <Transition name="popup-up">
                        <div
                            v-if="userMenuOpen"
                            v-click-outside="() => userMenuOpen = false"
                            :class="[
                                'absolute bottom-full mb-2 z-50 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1.5 overflow-hidden',
                                sidebarOpen ? 'left-0 right-0' : 'left-full ml-3 w-52',
                            ]"
                        >
                            <!-- User info header -->
                            <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">{{ user?.name }}</p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 truncate mt-0.5">{{ user?.email }}</p>
                            </div>

                            <!-- Profile link -->
                            <Link
                                :href="route('profile.show')"
                                class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                @click="userMenuOpen = false"
                            >
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profile
                            </Link>

                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"/>

                            <!-- Logout -->
                            <button
                                @click="logout"
                                class="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Log Out
                            </button>
                        </div>
                    </Transition>
                </div>
            </div>
        </aside>

        <!-- ══════════════════ MAIN AREA ══════════════════ -->
        <div
            :class="[
                'flex flex-col flex-1 min-h-screen transition-all duration-300 ease-in-out',
                sidebarOpen ? 'sm:ml-64' : 'sm:ml-[68px]',
            ]"
        >
            <!-- Top bar -->
            <header class="sticky top-0 z-20 flex items-center h-16 px-4 sm:px-6 bg-white/80 dark:bg-gray-900/80 backdrop-blur border-b border-gray-100 dark:border-gray-800 gap-4">
                
                <!-- Hamburger / collapse toggle -->
                <button
                    class="hidden sm:flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    @click="sidebarOpen = !sidebarOpen"
                    :title="sidebarOpen ? 'Tutup sidebar' : 'Buka sidebar'"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Mobile hamburger -->
                <button
                    class="sm:hidden flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    @click="mobileOpen = !mobileOpen"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Page title slot -->
                <div class="flex-1">
                    <slot name="header" />
                </div>
<NotificationBell />
                <!-- Teams dropdown (if needed) -->
                <Dropdown
                    v-if="$page.props.jetstream?.hasTeamFeatures"
                    align="right"
                    width="60"
                >
                    <template #trigger>
                        <button class="flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            {{ $page.props.auth.user.current_team?.name }}
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </template>
                    <template #content>
                        <div class="w-60">
                            <div class="block px-4 py-2 text-xs text-gray-400">Manage Team</div>
                            <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">Team Settings</DropdownLink>
                            <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">Create New Team</DropdownLink>
                            <template v-if="$page.props.auth.user.all_teams?.length > 1">
                                <div class="border-t border-gray-200 dark:border-gray-600"/>
                                <div class="block px-4 py-2 text-xs text-gray-400">Switch Teams</div>
                                <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                    <form @submit.prevent="switchToTeam(team)">
                                        <DropdownLink as="button">
                                            <div class="flex items-center gap-2">
                                                <svg v-if="team.id == $page.props.auth.user.current_team_id" class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                {{ team.name }}
                                            </div>
                                        </DropdownLink>
                                    </form>
                                </template>
                            </template>
                        </div>
                    </template>
                </Dropdown>
            </header>

            <!-- Page content -->
            <main class="flex-grow">
                <slot />
            </main>

            <Footer />
        </div>
    </div>
</template>

<style scoped>
.slide-fade-enter-active {
    transition: all 0.2s ease;
}
.slide-fade-leave-active {
    transition: all 0.15s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
    opacity: 0;
    transform: translateX(-6px);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.popup-up-enter-active {
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.popup-up-leave-active {
    transition: all 0.15s ease;
}
.popup-up-enter-from,
.popup-up-leave-to {
    opacity: 0;
    transform: translateY(8px) scale(0.96);
}
</style>