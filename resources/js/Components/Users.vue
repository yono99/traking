<template>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <h1 class="text-2xl font-semibold text-gray-900">Daftar Pengguna</h1>
        </div>

        <!-- Filter -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 my-4">
            <div class="flex flex-wrap gap-4">
                <input
                    v-model="filters.name"
                    placeholder="Filter Nama"
                    class="border border-gray-300 rounded px-4 py-2"
                />
                <input
                    v-model="filters.email"
                    placeholder="Filter Email"
                    class="border border-gray-300 rounded px-4 py-2"
                />
                <input
                    v-model="filters.role"
                    placeholder="Filter Role"
                    class="border border-gray-300 rounded px-4 py-2"
                />
                <input
                    v-model="filters.unit"
                    placeholder="Filter Unit"
                    class="border border-gray-300 rounded px-4 py-2"
                />
                <button
                    @click="resetFilter"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-500"
                >
                    Reset
                </button>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <div class="mt-8 flex flex-col">
                <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">No</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Unit</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="(user, index) in filteredUsers" :key="user.id">
                                        <td class="px-3 py-4 text-sm text-gray-500">{{ (currentPage - 1) * 10 + index + 1 }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500">{{ user.name }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500">{{ user.email }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500">{{ user.role || 'N/A' }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500">{{ user.unit || 'N/A' }}</td>
                                    </tr>
                                    <tr v-if="filteredUsers.length === 0">
                                        <td colspan="5" class="px-3 py-4 text-sm text-gray-500 text-center">
                                            Tidak ada data yang ditemukan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <button
                :disabled="currentPage === 1"
                @click="changePage(currentPage - 1)"
                class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400 disabled:opacity-50"
            >
                Previous
            </button>
            <span>Page {{ currentPage }} of {{ lastPage }}</span>
            <button
                :disabled="currentPage === lastPage"
                @click="changePage(currentPage + 1)"
                class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400 disabled:opacity-50"
            >
                Next
            </button>
        </div>
    </div>
</template>



<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// State untuk data pengguna dan pagination
const users = ref([]);
const currentPage = ref(1); // Halaman aktif
const lastPage = ref(1); // Total halaman
const totalUsers = ref(0); // Total pengguna

// State untuk menyimpan filter input
const filters = ref({
    name: '',
    email: '',
    role: '',
    unit: '',
});

// Fungsi untuk mengambil data pengguna dari server
const fetchUsers = async (page = 1) => {
    try {
        const response = await axios.get('/users', { params: { page } });
        users.value = response.data.data; // Data pengguna
        currentPage.value = response.data.current_page; // Halaman saat ini
        lastPage.value = response.data.last_page; // Total halaman
        totalUsers.value = response.data.total; // Total pengguna
        console.log('Fetched users:', users.value);
    } catch (error) {
        console.error('Error fetching users:', error.response ? error.response.data : error.message);
    }
};

// Computed property untuk memfilter data pengguna
const filteredUsers = computed(() => {
    return users.value.filter(user => {
        return (
            (!filters.value.name || user.name.toLowerCase().includes(filters.value.name.toLowerCase())) &&
            (!filters.value.email || user.email.toLowerCase().includes(filters.value.email.toLowerCase())) &&
            (!filters.value.role || user.role?.toLowerCase().includes(filters.value.role.toLowerCase())) &&
            (!filters.value.unit || user.unit?.toLowerCase().includes(filters.value.unit.toLowerCase()))
        );
    });
});

// Fungsi untuk mereset filter
const resetFilter = () => {
    filters.value = {
        name: '',
        email: '',
        role: '',
        unit: '',
    };
    fetchUsers(); // Reload data setelah reset filter
};

// Fungsi untuk pindah halaman
const changePage = (page) => {
    if (page >= 1 && page <= lastPage.value) {
        fetchUsers(page); // Ambil data pengguna untuk halaman tertentu
    }
};

// Ambil data pengguna saat komponen dipasang
onMounted(() => fetchUsers(currentPage.value));

</script>

<style scoped>
input {
    transition: all 0.2s;
    padding: 0.5rem;
    font-size: 0.875rem;
    width: 200px;
}
button {
    font-size: 0.875rem;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    text-align: left;
    padding: 0.75rem;
}
</style>