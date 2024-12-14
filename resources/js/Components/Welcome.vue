<script>

import { ref } from "vue";
import axios from "axios";
import { useForm } from "@inertiajs/inertia-vue3";

const parseFlatArrayToObjects = (flatArray) => {
    const result = [];
    for (let i = 0; i < flatArray.length; i += 8) {
        result.push({
            nomer_hak: flatArray[i],
            jenis_hak: flatArray[i + 1],
            desa_kecamatan: flatArray[i + 2],
            user_name: flatArray[i + 3],
            activity_status: flatArray[i + 4],
            service_name: flatArray[i + 5],
            service_contact: flatArray[i + 6],
            update_at: flatArray[i + 7],
        });
    }
    return result;
};

export default {
    setup() {
        const form = useForm({
            desa_kecamatan: "",
            jenis_hak: "",
            nomer_hak: "",
        });

        const resultData = ref([]);
        const showResult = ref(false);
        const loading = ref(false);
        const errorMessage = ref("");

        const desaKecamatanList = [
             "Balaraja - Balaraja",
            "Cangkudu - Balaraja",
            "Gembong - Balaraja",
            "Saga - Balaraja",
            "Sentul - Balaraja",
            "Sentul Jaya - Balaraja",
            "Sukamurni - Balaraja",
            "Talagasari - Balaraja",
            "Tobat - Balaraja",
            "Bitung Jaya - Cikupa",
            "Bojong - Cikupa",
            "Budi Mulya - Cikupa",
            "Bunder - Cikupa",
            "Cibadak - Cikupa",
            "Cikupa - Cikupa",
            "Dukuh - Cikupa",
            "Pasir Gadung - Cikupa",
            "Pasir Jaya - Cikupa",
            "Sukadamai - Cikupa",
            "Sukamulya - Cikupa",
            "Sukanagara - Cikupa",
            "Talaga - Cikupa",
            "Talagasari - Cikupa",
            "Cibogo - Cisauk",
            "Cisauk - Cisauk",
            "Dangdang - Cisauk",
            "Mekar Wangi - Cisauk",
            "Sampora - Cisauk",
            "Suradita - Cisauk",
            "Bojong Loa - Cisoka",
            "Carenang - Cisoka",
            "Caringin - Cisoka",
            "Cempaka - Cisoka",
            "Cibugel - Cisoka",
            "Cisoka - Cisoka",
            "Jeungjing - Cisoka",
            "Karangharja - Cisoka",
            "Selapajang - Cisoka",
            "Sukatani - Cisoka",
            "Binong - Curug",
            "Cukanggalih - Curug",
            "Curug Kulon - Curug",
            "Curug Wetan - Curug",
            "Kadu - Curug",
            "Kadu Jaya - Curug",
            "Sukabakti - Curug",
            "Cibetok - Gunung Kaler",
            "Cipaeh - Gunung Kaler",
            "Gunung Kaler - Gunung Kaler",
            "Kandawati - Gunung Kaler",
            "Kedung - Gunung Kaler",
            "Onyam - Gunung Kaler",
            "Rancagede - Gunung Kaler",
            "Sidoko - Gunung Kaler",
            "Tamiang - Gunung Kaler",
            "Ancol Pasir - Jambe",
            "Daru - Jambe",
            "Jambe - Jambe",
            "Kutruk - Jambe",
            "Mekarsari - Jambe",
            "Pasir Barat - Jambe",
            "Ranca Buaya - Jambe",
            "Tipar Raya - Jambe",
            "Sukamanah - Jambe",
            "Taban - Jambe",
            "Cikande - Jayanti",
            "Dangdeur - Jayanti",
            "Gintung - Jayanti",
            "Jayanti - Jayanti",
            "Pabuaran - Jayanti",
            "Pangkat - Jayanti",
            "Pasir - Jayanti",
            "Pasir Muncang - Jayanti",
            "Sumur Bandung - Jayanti",
            "Bencongan - Kelapa Dua",
            "Bencongan Indah - Kelapa Dua",
            "Bojong Nangka - Kelapa Dua",
            "Curug Sangereng - Kelapa Dua",
            "Kelapa Dua - Kelapa Dua",
            "Pakulonan Barat - Kelapa Dua",
            "Karang Anyar - Kemiri",
            "Kemiri - Kemiri",
            "Klebet - Kemiri",
            "Legok Sukamaju - Kemiri",
            "Lontar - Kemiri",
            "Patramanggala - Kemiri",
            "Ranca Labuh - Kemiri",
            "Belimbing - Kosambi",
            "Cengklong - Kosambi",
            "Dadap - Kosambi",
            "Jati Mulya - Kosambi",
            "Kosambi Barat - Kosambi",
            "Kosambi Timur - Kosambi",
            "Rawa Burung - Kosambi",
            "Rawa Rengas - Kosambi",
            "Salembaran Jati - Kosambi",
            "Salembaran Jaya - Kosambi",
            "Jengkol - Kresek",
            "Kemuning - Kresek",
            "Koper - Kresek",
            "Kresek - Kresek",
            "Pasir Ampo - Kresek",
            "Patrasana - Kresek",
            "Rancailat - Kresek",
            "Renged - Kresek",
            "Talok - Kresek",
            "Bakung - Kronjo",
            "Blukbuk - Kronjo",
            "Cirumpak - Kronjo",
            "Kronjo - Kronjo",
            "Muncung - Kronjo",
            "Pagedangan Ilir - Kronjo",
            "Pagedangan Udik - Kronjo",
            "Pagenjahan - Kronjo",
            "Pasilian - Kronjo",
            "Pasir - Kronjo",
            "Babakan - Legok",
            "Babat - Legok",
            "Bojong Kamal - Legok",
            "Caringin - Legok",
            "Ciangir - Legok",
            "Cirarab - Legok",
            "Kemuning - Legok",
            "Legok - Legok",
            "Palasari - Legok",
            "Rancagong - Legok",
            "Serdang Wetan - Legok",
            "Banyu Asih - Mauk",
            "Gunung Sari - Mauk",
            "Jatiwaringin - Mauk",
            "Kedung Dalam - Mauk",
            "Ketapang - Mauk",
            "Marga Mulya - Mauk",
            "Mauk Barat - Mauk",
            "Mauk Timur - Mauk",
            "Sasak - Mauk",
            "Tanjung Anom - Mauk",
            "Tegal Kunir Kidul - Mauk",
            "Tegal Kunir Lor - Mauk",
            "Cijeruk - Mekarbaru",
            "Gandaria - Mekarbaru",
            "Jenggot - Mekarbaru",
            "Kedaung - Mekarbaru",
            "Klutuk - Mekarbaru",
            "Kosambi Dalam - Mekarbaru",
            "Mekarbaru - Mekarbaru",
            "Waliwis - Mekarbaru",
            "Cicalengka - Pagedangan",
            "Cihuni - Pagedangan",
            "Cijantra - Pagedangan",
            "Jatake - Pagedangan",
            "Kadusirung - Pagedangan",
            "Karang Tengah - Pagedangan",
            "Lengkong Kulon - Pagedangan",
            "Malangnengah - Pagedangan",
            "Medang - Pagedangan",
            "Pagedangan - Pagedangan",
            "Situ Gadung - Pagedangan",
            "Bonisari - Pakuhaji",
            "Buaran Bambu - Pakuhaji",
            "Buaran Mangga - Pakuhaji",
            "Gaga - Pakuhaji",
            "Kalibaru - Pakuhaji",
            "Kiara Payung - Pakuhaji",
            "Kohod - Pakuhaji",
            "Kramat - Pakuhaji",
            "Laksana - Pakuhaji",
            "Paku Alam - Pakuhaji",
            "Pakuhaji - Pakuhaji",
            "Rawa Boni - Pakuhaji",
            "Sukawali - Pakuhaji",
            "Surya Bahari - Pakuhaji",
            "Ciakar - Panongan",
            "Mekar Jaya - Panongan",
            "Mekarbakti - Panongan",
            "Panongan - Panongan",
            "Peusar - Panongan",
            "Ranca Iyuh - Panongan",
            "Ranca Kalapa - Panongan",
            "Serdang Kulon - Panongan",
            "Gelam Jaya - Pasar Kemis",
            "Kuta Jaya - Pasar Kemis",
            "Kutabaru - Pasar Kemis",
            "Kutabumi - Pasar Kemis",
            "Pangadegan - Pasar Kemis",
            "Pasar Kemis - Pasar Kemis",
            "Sindangsari - Pasar Kemis",
            "Suka Asih - Pasar Kemis",
            "Sukamantri - Pasar Kemis",
            "Daon - Rajeg",
            "Jambu Karya - Rajeg",
            " - Rajeg",
            "Lembangsari - Rajeg",
            "Mekarsari - Rajeg",
            "Pangarengan - Rajeg",
            "Rajeg - Rajeg",
            "Rajeg Mulya - Rajeg",
            "Ranca Bango - Rajeg",
            "Sukamanah - Rajeg",
            "Sukasari - Rajeg",
            "Sukatani - Rajeg",
            "Tanjakan - Rajeg",
            "Tanjakan Mekar - Rajeg",
            "Karet - Sepatan",
            "Kayu Agung - Sepatan",
            "Kayu Bongkok - Sepatan",
            "Mekar Jaya - Sepatan",
            "Pisangan Jaya - Sepatan",
            "Pondok Jaya - Sepatan",
            "Sarakan - Sepatan",
            "Sepatan - Sepatan",
            "Gempol Sari - Sepatan Timur",
            "Jati Mulya - Sepatan Timur",
            "Kampung Kelor - Sepatan Timur",
            "Kedaung Barat - Sepatan Timur",
            "Lebak Wangi - Sepatan Timur",
            "Pondok Kelor - Sepatan Timur",
            "Sangiang - Sepatan Timur",
            "Tanah Merah - Sepatan Timur",
            "Badak Anom - Sindang Jaya",
            "Sindang Jaya - Sindang Jaya",
            "Sindangasih - Sindang Jaya",
            "Sindangpanon - Sindang Jaya",
            "Sindangsono - Sindang Jaya",
            "Sukaharja - Sindang Jaya",
            "Wanakerta - Sindang Jaya",
            "Cikareo - Solear",
            "Cikasungka - Solear",
            "Cikuya - Solear",
            "Cireundeu - Solear",
            "Munjul - Solear",
            "Pasanggrahan - Solear",
            "Solear - Solear",
            "Buaran Jati - Sukadiri",
            "Gintung - Sukadiri",
            "Karang Serang - Sukadiri",
            "Kosambi - Sukadiri",
            "Mekar Kondang - Sukadiri",
            "Pekayon - Sukadiri",
            "Rawa Kidang - Sukadiri",
            "Sukadiri - Sukadiri",
            "Benda - Sukamulya",
            "Bunar - Sukamulya",
            "Buniayu - Sukamulya",
            "Kaliasin - Sukamulya",
            "Kubang - Sukamulya",
            "Merak - Sukamulya",
            "Parahu - Sukamulya",
            "Sukamulya - Sukamulya",
            "Asem - Teluknaga",
            "Babakan Asem - Teluknaga",
            "Bojong Renged - Teluknaga",
            "Kampung Besar - Teluknaga",
            "Kampung Melayu Barat - Teluknaga",
            "Kampung Melayu Timur - Teluknaga",
            "Keboncau - Teluknaga",
            "Lemo - Teluknaga",
            "Muara - Teluknaga",
            "Pangkalan - Teluknaga",
            "Tanjung Burung - Teluknaga",
            "Tanjung Pasir - Teluknaga",
            "Tegal Angus - Teluknaga",
            "Teluknaga - Teluknaga",
            "Bantar Panjang - Tigaraksa",
            "Cileles - Tigaraksa",
            "Cisereh - Tigaraksa",
            "Kadu Agung - Tigaraksa",
            "Margasari - Tigaraksa",
            "Matagara - Tigaraksa",
            "Pasir Bolang - Tigaraksa",
            "Pasir Nangka - Tigaraksa",
            "Pematang - Tigaraksa",
            "Pete - Tigaraksa",
            "Sodong - Tigaraksa",
            "Tapos - Tigaraksa",
            "Tegalsari - Tigaraksa",
            "Tigaraksa - Tigaraksa",
        ];

        const searchQuery = ref("");
        const showDropdown = ref(false);
        const filteredLocations = ref([]);
        const selectedIndex = ref(-1);

        const filterLocations = (query) => {
            if (!query) {
                filteredLocations.value = [];
                return;
            }
            const searchTerm = query.toLowerCase();
            filteredLocations.value = desaKecamatanList.filter((location) =>
                location.toLowerCase().includes(searchTerm)
            );
            selectedIndex.value = -1;
        };

        const handleInput = (e) => {
            searchQuery.value = e.target.value;
            form.desa_kecamatan = e.target.value;
            filterLocations(searchQuery.value);
            showDropdown.value = true;
        };

        const selectLocation = (location) => {
            searchQuery.value = location;
            form.desa_kecamatan = location;
            showDropdown.value = false;
            selectedIndex.value = -1;
        };

        const handleKeydown = (e) => {
            if (!showDropdown.value || filteredLocations.value.length === 0)
                return;

            switch (e.key) {
                case "ArrowDown":
                    e.preventDefault();
                    selectedIndex.value =
                        selectedIndex.value < filteredLocations.value.length - 1
                            ? selectedIndex.value + 1
                            : 0;
                    break;
                case "ArrowUp":
                    e.preventDefault();
                    selectedIndex.value =
                        selectedIndex.value > 0
                            ? selectedIndex.value - 1
                            : filteredLocations.value.length - 1;
                    break;
                case "Enter":
                    e.preventDefault();
                    if (selectedIndex.value > -1) {
                        selectLocation(
                            filteredLocations.value[selectedIndex.value]
                        );
                    } else if (filteredLocations.value.length > 0) {
                        selectLocation(filteredLocations.value[0]);
                    }
                    break;
                case "Escape":
                    showDropdown.value = false;
                    selectedIndex.value = -1;
                    break;
            }
        };

        const submitForm = async () => {
            errorMessage.value = "";
            loading.value = true;

            // Menghilangkan leading zeros pada 'nomer_hak'
            form.nomer_hak = form.nomer_hak.replace(/^0+/, ""); // Menghapus semua nol di depan

            try {
                const response = await axios.get("/activities/fetch", {
                    params: {
                        desa_kecamatan: form.desa_kecamatan,
                        jenis_hak: form.jenis_hak,
                        nomer_hak: form.nomer_hak, // Sudah dimodifikasi tanpa leading zeros
                    },
                });

                resultData.value = parseFlatArrayToObjects(response.data);
                showResult.value = true;
            } catch (error) {
                console.error("Error fetching data:", error);
                errorMessage.value =
                    "Data tidak dapat diambil. Silakan coba lagi.";
            } finally {
                loading.value = false;
            }
        };

        return {
            form,
            resultData,
            showResult,
            loading,
            errorMessage,
            submitForm,
            searchQuery,
            showDropdown,
            filteredLocations,
            selectedIndex,
            handleInput,
            selectLocation,
            handleKeydown,
        };
    },
  
 
};
</script>
 
<template>
    
                                                         

    <div
        class="p-6 lg:p-9 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700"
    >
        <h1 class="text-2xl font-medium text-gray-900 dark:text-white mb-6">
            Ada yang ingin dicari ? 
        </h1>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Input Desa/Kecamatan dengan Autocomplete -->
            <div>
                <label
                    for="desa_kecamatan"
                    class="block text-sm font-medium text-gray-900 dark:text-white"
                >
                    Desa - Kecamatan
                </label>
                <input
                    type="text"
                    id="desa_kecamatan"
                    v-model="searchQuery"
                    @input="handleInput"
                    @keydown="handleKeydown"
                    placeholder="Masukkan Desa - Kecamatan..."
                    class="mt-1 block w-full border border-gray-300 rounded p-2"
                    required
                />
                <!-- Dropdown -->
                <div
                    v-if="showDropdown "
                    class="absolute bg-white border mt-1 rounded shadow "
                >
                    <ul>
                        <li
                            v-for="(location, index) in filteredLocations"
                            :key="index"
                            :class="{ 'bg-gray-200': index === selectedIndex }"
                            @click="selectLocation(location)"
                            class="px-4 py-2 cursor-pointer hover:bg-gray-200"
                        >
                            {{ location }}
                        </li>
                    </ul>
                </div>
            </div>

            <div>
                <label
                    for="jenis_hak"
                    class="block text-sm font-medium text-gray-900 dark:text-white"
                >
                    Jenis Hak
                </label>
                <select
                    id="jenis_hak"
                    v-model="form.jenis_hak"
                    class="mt-1 block w-full border border-gray-300 rounded p-2"
                    required
                >
                    <option value="" disabled>Pilih Jenis Hak</option>
                    <option value="HGB">HGB</option>
                    <option value="HM">HM</option>
                    <option value="HP">HP</option>
                    <option value="HMRS">HMRS</option>
                    <option value="HW">HW</option>
                </select>
            </div>

            <div>
                <label
                    for="nomer_hak"
                    class="block text-sm font-medium text-gray-900 dark:text-white"
                >
                    Nomer Hak
                </label>
                <input
                    type="text"
                    id="nomer_hak"
                    maxlength="5"
                    v-model="form.nomer_hak"
                    placeholder="Masukkan Nomer Hak..."
                    class="mt-1 block w-full border border-gray-300 rounded p-2"
                    required
                    @input="
                        form.nomer_hak = form.nomer_hak
                            .replace(/\D/g, '')
                            .slice(0, 5)
                    "
                />
            </div>

            <button
                type="submit"
                class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                :disabled="loading"
            >
                {{ loading ? "Memuat..." : "Submit" }}
            </button>
        </form>

        <!-- Error Message -->
        <div v-if="errorMessage" class="text-red-500 mt-4">
            {{ errorMessage }}
        </div>

        <!-- Result Table -->
        <div v-if="showResult" class="mt-6 bg-gray-100 p-4 rounded shadow-md">
            <h2 class="text-xl font-medium text-gray-800 mb-4">
                History Aktivitas
            </h2>

            <div
                v-if="resultData.length === 0"
                class="text-center text-gray-500"
            >
                Tidak ada data yang ditemukan untuk filter yang diberikan.
            </div>

            <div v-e lse class="overflow-x-auto">
                <table
                    class="table-auto w-full border-collapse border border-gray-300"
                >
                    <thead class="bg-gray-200">
                        <tr>
                            <th
                                class="border border-gray-300 px-4 py-2 text-left"
                            >
                                No. Hak
                            </th>
                            <th
                                class="border border-gray-300 px-4 py-2 text-left"
                            >
                                Jenis Hak
                            </th>
                            <th
                                class="border border-gray-300 px-4 py-2 text-left"
                            >
                                Desa/Kecamatan
                            </th>
                            <th
                                class="border border-gray-300 px-4 py-2 text-left"
                            >
                                Nama Petugas
                            </th>
                            <th
                                class="border border-gray-300 px-4 py-2 text-left"
                            >
                                Status Aktivitas
                            </th>
                            <th
                                class="border border-gray-300 px-4 py-2 text-left"
                            >
                                Nama Layanan
                            </th>
                            <th
                                class="border border-gray-300 px-4 py-2 text-left"
                            >
                                Kontak
                            </th>
                            <th
                                class="border border-gray-300 px-4 py-2 text-left"
                            >
                                Waktu
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(activity, index) in resultData"
                            :key="index"
                            class="odd:bg-white even:bg-gray-50"
                        >
                            <td class="border border-gray-300 px-4 py-2">
                                {{ activity.nomer_hak }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ activity.jenis_hak }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ activity.desa_kecamatan }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ activity.user_name }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ activity.activity_status }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ activity.service_name }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                0{{ activity.service_contact }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ activity.update_at }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>


<style>
/* Box container styling untuk grid */
.box-container {
  display: grid; /* Menggunakan grid */
  grid-template-columns: repeat(2, 1fr); /* Membuat 2 kolom dengan lebar yang sama */
  gap: 1rem; /* Jarak antar kotak */
  justify-items: center; /* Menempatkan item di tengah secara horizontal */
  align-items: center; /* Menempatkan item di tengah secara vertikal */
  padding: 2rem; /* Padding untuk seluruh container */
}

/* Styling untuk setiap box */
.box {
  background-color: #f0f0f0; /* Warna latar belakang kotak */
  border: 1px solid #ddd; /* Border abu-abu untuk kotak */
  padding: 1rem; /* Padding di dalam kotak */
  border-radius: 8px; /* Sudut kotak yang membulat */
  display: flex; /* Flexbox untuk konten di dalam box */
  justify-content: space-between; /* Memisahkan label dan count */
  align-items: center; /* Vertikal align */
  width: 200px; /* Menentukan lebar kotak */
}

/* Styling untuk label di dalam box */
.label {
  font-weight: bold;
  color: #333; /* Warna teks label */
}

/* Styling untuk count di dalam box */
.count {
  font-size: 1.25rem;
  color: #007BFF; /* Warna teks untuk count */
}

</style>
