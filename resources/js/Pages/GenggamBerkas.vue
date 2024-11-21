<script>
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
    layout: AppLayout,
    setup() {
        const form = useForm({
            nomer_hak: "",
            desa_kecamatan: "",
            nomor_hp: "",
            jenis_hak: "",
        });

        // Data untuk autocomplete
        const desaKecamatanList = [
            "Pasarkemis - Pasarkemis",
            "Gelam Jaya - Pasarkemis",
            "Sindang Sari - Pasarkemis",
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

        // State untuk autocomplete
        const searchQuery = ref("");
        const showDropdown = ref(false);
        const filteredLocations = ref([]);
        const selectedIndex = ref(-1); // Untuk tracking item yang dipilih dengan keyboard

        // Method untuk filter lokasi berdasarkan input
        const filterLocations = (query) => {
            if (!query) {
                filteredLocations.value = [];
                return;
            }

            const searchTerm = query.toLowerCase();
            filteredLocations.value = desaKecamatanList.filter((location) =>
                location.toLowerCase().includes(searchTerm)
            );
            selectedIndex.value = -1; // Reset selected index when filtering
        };

        // Method untuk handle input change
        const handleInput = (e) => {
            searchQuery.value = e.target.value;
            form.desa_kecamatan = e.target.value;
            filterLocations(searchQuery.value);
            showDropdown.value = true;
        };

        // Method untuk memilih lokasi dari dropdown
        const selectLocation = (location) => {
            searchQuery.value = location;
            form.desa_kecamatan = location;
            showDropdown.value = false;
            selectedIndex.value = -1;
        };

        // Method untuk handle keyboard navigation
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
                    // Jika ada item yang dipilih, pilih item tersebut
                    if (selectedIndex.value > -1) {
                        selectLocation(
                            filteredLocations.value[selectedIndex.value]
                        );
                    } else {
                        // Jika tidak ada item yang dipilih, pilih item yang pertama yang cocok dengan query
                        if (filteredLocations.value.length > 0) {
                            selectLocation(filteredLocations.value[0]);
                        }
                    }
                    break;
                case "Escape":
                    showDropdown.value = false;
                    selectedIndex.value = -1;
                    break;
            }
        };

        // Validasi error
        const errors = ref({
            nomer_hak: "",
            desa_kecamatan: "",
            nomor_hp: "",
            jenis_hak: "",
        });

        const submitForm = () => {
            // Reset pesan error
            errors.value = {
                nomer_hak: "",
                desa_kecamatan: "",
                nomor_hp: "",
                jenis_hak: "",
            };

            // Validasi manual
            let hasError = false;
            if (!form.nomer_hak) {
                errors.value.nomer_hak = "Nomer Hak wajib diisi.";
                hasError = true;
            }
            if (!form.desa_kecamatan) {
                errors.value.desa_kecamatan = "Desa/Kecamatan wajib dipilih.";
                hasError = true;
            }
            if (!form.nomor_hp) {
                errors.value.nomor_hp = "Nomor HP wajib diisi.";
                hasError = true;
            }
            if (!form.jenis_hak) {
                errors.value.jenis_hak = "Jenis Hak wajib dipilih.";
                hasError = true;
            }

            // Jika ada error, jangan kirim form
            if (hasError) return;

            // Kirim form jika tidak ada error
            form.post("/genggam-berkas", {
                onSuccess: () => {
                    form.reset();
                    searchQuery.value = "";
                },
            });
        };

        return {
            form,
            errors,
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
    <div class="p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Input Data Genggam Berkas</h2>
        <form @submit.prevent="submitForm">
            <!-- Input Nomor HP -->
            <div class="mb-4">
                <label
                    for="nomor_hp"
                    class="block text-sm font-medium text-gray-700"
                    >Nomor HP</label
                >
                <p class="text-gray-500 text-sm">8xxxx tanpa +62/0</p>
                <input
                    type="text"
                    placeholder="8xxxx"
                    id="nomor_hp"
                    v-model="form.nomor_hp"
                    maxlength="12"
                    @input="
                        form.nomor_hp = form.nomor_hp
                            .replace(/\D/g, '')
                            .slice(0, 12)
                    "
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                    required
                />

                <p v-if="errors.nomor_hp" class="text-red-500 text-sm">
                    {{ errors.nomor_hp }}
                </p>
            </div>

            <!-- Autocomplete Input untuk Desa/Kecamatan -->
            <div class="mb-4 relative">
                <label
                    for="desa_kecamatan"
                    class="block text-sm font-medium text-gray-700"
                    >Desa/Kecamatan</label
                >
                <input
                    type="text"
                    id="desa_kecamatan"
                    v-model="searchQuery"
                    @input="handleInput"
                    @focus="showDropdown = true"
                    @keydown="handleKeydown"
                    placeholder="Ketik untuk mencari..."
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                    required
                />
                <!-- Dropdown suggestions -->
                <div
                    v-if="showDropdown && filteredLocations.length > 0"
                    class="absolute z-10 w-full bg-white mt-1 rounded-md shadow-lg border border-gray-200"
                >
                    <ul class="max-h-60 overflow-auto">
                        <li
                            v-for="(location, index) in filteredLocations"
                            :key="location"
                            @click="selectLocation(location)"
                            :class="[
                                'px-4 py-2 cursor-pointer',
                                selectedIndex === index
                                    ? 'bg-blue-100'
                                    : 'hover:bg-gray-100',
                            ]"
                        >
                            {{ location }}
                        </li>
                    </ul>
                </div>
                <p v-if="errors.desa_kecamatan" class="text-red-500 text-sm">
                    {{ errors.desa_kecamatan }}
                </p>
            </div>

            <!-- Dropdown Jenis Hak -->
            <div class="mb-4">
                <label
                    for="jenis_hak"
                    class="block text-sm font-medium text-gray-700"
                    >Jenis Hak</label
                >
                <select
                    id="jenis_hak"
                    v-model="form.jenis_hak"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                    required
                >
                    <option value="" disabled>Pilih Jenis Hak</option>
                    <option value="HGB">HGB</option>
                    <option value="HM">HM</option>
                    <option value="HMRS">HMRS</option>
                    <option value="HP">HP</option>
                    <option value="HW">HW</option>
                </select>
                <p v-if="errors.jenis_hak" class="text-red-500 text-sm">
                    {{ errors.jenis_hak }}
                </p>
            </div>

            <!-- Input Nomer Hak -->
            <div class="mb-4">
                <label
                    for="nomer_hak"
                    class="block text-sm font-medium text-gray-700"
                    >Nomer Hak</label
                >
                <input
                    type="text"
                    id="nomer_hak"
                    v-model="form.nomer_hak"
                    maxlength="5"
                    @input="
                        form.nomer_hak = form.nomer_hak
                            .replace(/\D/g, '')
                            .slice(0, 5)
                    "
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                    required
                />

                <p v-if="errors.nomer_hak" class="text-red-500 text-sm">
                    {{ errors.nomer_hak }}
                </p>
            </div>

            <!-- Tombol Submit -->
            <button
                type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
            >
                Submit
            </button>
        </form>
    </div>
</template>
