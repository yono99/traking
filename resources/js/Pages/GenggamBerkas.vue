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

            // Hapus awalan nol sebelum mengirim ke server
            const nomerHakWithoutLeadingZero = form.nomer_hak.replace(
                /^0+/,
                ""
            );

            // Kirim form jika tidak ada error
            form.nomer_hak = nomerHakWithoutLeadingZero; // Update nomer_hak dengan nilai tanpa nol
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
                    @input="
                        form.nomer_hak = form.nomer_hak
                            .replace(/\D/g, '')
                            .slice(0, 5)
                    "
                    v-model="form.nomer_hak"
                    maxlength="5"
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
