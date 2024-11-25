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
            "PASARKEMIS - PASARKEMIS",
            "Gelam Jaya - PASARKEMIS",
            "Sindang Sari - PASARKEMIS",
            "TEGALANGUS - TELUKNAGA",
            "TEGAL KUNIR LOR - MAUK",
            "TEGAL KUNIR KIDUL - MAUK",
            "TAPOS - TIGARAKSA",
            "TANJUNG PASIR - TELUKNAGA",
            "TANJUNG BURUNG - TELUKNAGA",
            "TANJUNG ANOM - MAUK",
            "TANJAKAN MEKAR - RAJEG",
            "TANJAKAN - RAJEG",
            "TANAH MERAH - SEPATAN TIMUR",
            "TAMIANG - GUNUNG KALER",
            "TALOK - KRESEK",
            "TALAGASARI - BALARAJA",
            "TALAGASARI - CIKUPA",
            "TALAGA - CIKUPA",
            "TABAN - JAMBE",
            "SURYA BAHARI - PAKUHAJI",
            "SURADITA - CISAUK",
            "SUMUR BANDUNG - JAYANTI",
            "SUKAWALI - PAKUHAJI",
            "SUKATANI - CISOKA",
            "SUKATANI - RAJEG",
            "SUKASARI - RAJEG",
            "SUKANAGARA - CIKUPA",
            "SUKAMURNI - BALARAJA",
            "SUKAMULYA - CIKUPA",
            "SUKAMULYA - SUKAMULYA",
            "SUKAMANTRI - PASAR KEMIS",
            "SUKAMANAH. - JAMBE",
            "SUKAMANAH - RAJEG",
            "SUKAHARJA - SINDANG JAYA",
            "SUKADIRI - SUKADIRI",
            "SUKADAMAI - CIKUPA",
            "SUKABAKTI - CURUG",
            "SUKA ASIH - PASAR KEMIS",
            "SOLEAR - SOLEAR",
            "SODONG - TIGARAKSA",
            "SITUGADUNG - PAGEDANGAN",
            "SINDANGSONO - SINDANG JAYA",
            "SINDANGSARI - PASAR KEMIS",
            "SINDANG PANON - SINDANG JAYA",
            "SINDANG JAYA - SINDANG JAYA",
            "SINDANG ASIH - SINDANG JAYA",
            "SIDOKO - GUNUNG KALER",
            "SERDANG WETAN - LEGOK",
            "SERDANG KULON - PANONGAN",
            "SEPATAN - SEPATAN",
            "SENTUL JAYA - BALARAJA",
            "SENTUL - BALARAJA",
            "SELAPAJANG - CISOKA",
            "SASAK - MAUK",
            "SARAKAN - SEPATAN",
            "SANGIANG - SEPATAN TIMUR",
            "SAMPORA - CISAUK",
            "SALEMBARAN JAYA - KOSAMBI",
            "SALEMBARAN JATI - KOSAMBI",
            "SAGA - BALARAJA",
            "RENGED - KRESEK",
            "RAWARENGAS - KOSAMBI",
            "RAWA KIDANG - SUKADIRI",
            "RAWA BURUNG - KOSAMBI",
            "RAWA BONI - PAKUHAJI",
            "RANCAKALAPA - PANONGAN",
            "RANCAIYUH - PANONGAN",
            "RANCAILAT - KRESEK",
            "RANCAGONG - LEGOK",
            "RANCAGEDE - GUNUNG KALER",
            "RANCABANGO - RAJEG",
            "RANCA LABUH - KEMIRI",
            "RANCA BUAYA - JAMBE",
            "RAJEG MULYA - RAJEG",
            "RAJEG - RAJEG",
            "PONDOK KELOR - SEPATAN TIMUR",
            "PONDOK JAYA - SEPATAN",
            "PISANGAN JAYA - SEPATAN",
            "PEUSAR - PANONGAN",
            "PETE - TIGARAKSA",
            "PEMATANG - TIGARAKSA",
            "PEKAYON - SUKADIRI",
            "PATRASANA - KRESEK",
            "PATRAMANGGALA - KEMIRI",
            "PASIR NANGKA - TIGARAKSA",
            "PASIR MUNCANG - JAYANTI",
            "PASIR JAYA - CIKUPA",
            "PASIR GINTUNG - JAYANTI",
            "PASIR GADUNG - CIKUPA",
            "PASIR BOLANG - TIGARAKSA",
            "PASIR BARAT - JAMBE",
            "PASIR AMPO - KRESEK",
            "PASIR - KRONJO",
            "PASILIAN - KRONJO",
            "PASAR KEMIS - PASAR KEMIS",
            "PASANGGRAHAN - SOLEAR",
            "PARAHU - SUKAMULYA",
            "PANONGAN - PANONGAN",
            "PANGKAT - JAYANTI",
            "PANGKALAN - TELUKNAGA",
            "PANGARENGAN - RAJEG",
            "PANGADEGAN - PASAR KEMIS",
            "PALASARI - LEGOK",
            "PAKULONAN BARAT - KELAPA DUA",
            "PAKUHAJI - PAKUHAJI",
            "PAKUALAM - PAKUHAJI",
            "PAGENJAHAN - KRONJO",
            "PAGEDANGAN UDIK - KRONJO",
            "PAGEDANGAN ILIR - KRONJO",
            "PAGEDANGAN - PAGEDANGAN",
            "PABUARAN - JAYANTI",
            "ONYAM - GUNUNG KALER",
            "MUNJUL - SOLEAR",
            "MUNCUNG - MEKAR BARU",
            "MUNCUNG - KRONJO",
            "MUARA - TELUKNAGA",
            "MERAK - SUKAMULYA",
            "MEKARSARI. - RAJEG",
            "MEKARSARI - JAMBE",
            "MEKARJAYA - PANONGAN",
            "MEKARBAKTI - PANONGAN",
            "MEKAR WANGI - CISAUK",
            "MEKAR KONDANG - SUKADIRI",
            "MEKAR JAYA - SEPATAN",
            "MEKAR BARU - MEKAR BARU",
            "MEDANG - PAGEDANGAN",
            "MAUK TIMUR - MAUK",
            "MAUK BARAT - MAUK",
            "MATAGARA - TIGARAKSA",
            "MARGASARI - TIGARAKSA",
            "MARGA MULYA - MAUK",
            "MALANGNENGAH - PAGEDANGAN",
            "LONTAR - KEMIRI",
            "LENGKONG KULON - PAGEDANGAN",
            "LEMO - TELUKNAGA",
            "LEMBANGSARI - RAJEG",
            "LEGOK SUKAMAJU - KEMIRI",
            "LEGOK - LEGOK",
            "LEBAKWANGI - SEPATAN TIMUR",
            "LAKSANA - PAKUHAJI",
            "KUTRUK - JAMBE",
            "KUTAJAYA - PASAR KEMIS",
            "KUTABUMI - PASAR KEMIS",
            "KUTA BARU - PASAR KEMIS",
            "KUBANG - SUKAMULYA",
            "KRONJO - KRONJO",
            "KRESEK - KRESEK",
            "KRAMAT - PAKUHAJI",
            "KOSAMBI TIMUR - KOSAMBI",
            "KOSAMBI DALAM - MEKAR BARU",
            "KOSAMBI BARAT - KOSAMBI",
            "KOSAMBI - SUKADIRI",
            "KOPER - KRESEK",
            "KOHOD - PAKUHAJI",
            "KLUTUK - MEKAR BARU",
            "KLEBET - KEMIRI",
            "KIARA PAYUNG - PAKUHAJI",
            "KETAPANG - MAUK",
            "KEMUNING - LEGOK",
            "KEMUNING - KRESEK",
            "KEMIRI - KEMIRI",
            "KELAPA DUA - KELAPA DUA",
            "KEDUNG DALAM - MAUK",
            "KEDUNG - GUNUNG KALER",
            "KEDAUNG BARAT - SEPATAN TIMUR",
            "KEDAUNG - MEKAR BARU",
            "KEBON CAU - TELUKNAGA",
            "KAYU BONGKOK - SEPATAN",
            "KAYU AGUNG - SEPATAN",
            "KARET - SEPATAN",
            "KARANG TENGAH - PAGEDANGAN",
            "KARANG SERANG - SUKADIRI",
            "KARANG HARJA - CISOKA",
            "KARANG ANYAR - KEMIRI",
            "KANDAWATI - GUNUNG KALER",
            "KAMPUNG MELAYU TIMUR - TELUKNAGA",
            "KAMPUNG MELAYU BARAT - TELUKNAGA",
            "KAMPUNG KELOR - SEPATAN TIMUR",
            "KAMPUNG BESAR - TELUKNAGA",
            "KALIBARU - PAKUHAJI",
            "KALIASIN - SUKAMULYA",
            "KADUSIRUNG - PAGEDANGAN",
            "KADU JAYA - CURUG",
            "KADU AGUNG - TIGARAKSA",
            "KADU - CURUG",
            "JEUNGJING - CISOKA",
            "JENGKOL - KRESEK",
            "JENGGOT - MEKAR BARU",
            "JAYANTI - JAYANTI",
            "JATIWARINGIN - MAUK",
            "JATIMULYA. - SEPATAN TIMUR",
            "JATIMULYA - KOSAMBI",
            "JATAKE - PAGEDANGAN",
            "JAMBU KARYA - RAJEG",
            "JAMBE - JAMBE",
            "GUNUNG SARI - GUNUNG KALER",
            "GUNUNG LUBANG - GUNUNG KALER",
            "GRONGGONG - KRESEK",
            "GIRI KARYA - CISOKA",
            "GIRIMUKTI - MAUK",
            "GIRIAWAN - GUNUNG KALER",
            "GEMELANG - CISAUK",
            "GEMBONG - PAGEDANGAN",
            "GEDUNG WULUNG - PAKUHAJI",
            "GEDUNG PALE - PAKUHAJI",
            "GAJAH MUNGKUR - CISAUK",
            "ENGSENG - KRESEK",
            "ENGONGK - MAUK",
            "ENGKOL - JAMBE",
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
    form.nomer_hak = form.nomer_hak.replace(/^0+/, ''); // Menghapus semua nol di depan

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
        errorMessage.value = "Data tidak dapat diambil. Silakan coba lagi.";
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
            Selamat Datang di Aplikasi Genggamtanahku!
        </h1>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Input Desa/Kecamatan dengan Autocomplete -->
            <div>
                <label
                    for="desa_kecamatan"
                    class="block text-sm font-medium text-gray-900 dark:text-white"
                >
                    Desa/Kecamatan
                </label>
                <input
                    type="text"
                    id="desa_kecamatan"
                    v-model="searchQuery"
                    @input="handleInput"
                    @keydown="handleKeydown"
                    placeholder="Masukkan Desa/Kecamatan..."
                    class="mt-1 block w-full border border-gray-300 rounded p-2"
                    required
                />
                <!-- Dropdown -->
                <div
                    v-if="showDropdown"
                    class="absolute bg-white border mt-1 rounded shadow"
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
                    @input="form.nomer_hak = form.nomer_hak.replace(/\D/g, '').slice(0, 5)"
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
                                Nama Pengguna
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
                                {{ activity.service_contact }}
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
