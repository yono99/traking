<template>
    <div>
        <h1 class="text-2xl font-semibold mb-4">Tambah Layanan</h1>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <label for="name" class="block">Nama Layanan</label>
                <input type="text" id="name" v-model="form.name" class="mt-2 p-2 border rounded w-full" required />
            </div>

            <div class="mb-4">
                <label for="nomor_hp" class="block">Nomor HP</label>
                <input type="text" id="nomor_hp" v-model="form.nomor_hp" class="mt-2 p-2 border rounded w-full" required />
            </div>

            <div class="mb-4">
                <label for="land_book_id" class="block">Buku Tanah</label>
                <select id="land_book_id" v-model="form.land_book_id" class="mt-2 p-2 border rounded w-full" required>
                    <option value="">Pilih Buku Tanah</option>
                    <option v-for="landBook in landBooks" :key="landBook.id" :value="landBook.id">
                        {{ landBook.nomer_hak }}
                    </option>
                </select>
            </div>

            <div class="mb-4">
                <button type="submit" class="bg-blue-500 text-white p-2 rounded">Simpan Layanan</button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    nomor_hp: '',
    land_book_id: '',
});

const landBooks = ref([]);

// Mengambil data buku tanah untuk pilihan
onMounted(() => {
    axios.get('/api/land-books') // Pastikan ada rute API untuk mendapatkan buku tanah
        .then(response => {
            landBooks.value = response.data;
        });
});

// Submit form untuk menambah layanan
const submit = () => {
    form.post(route('services.store'));
};
</script>
