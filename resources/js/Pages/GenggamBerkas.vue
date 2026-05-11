<script>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";
 
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content;
export default {
    setup() {
        // ─── Step & Resume State ───────────────────────────────────
        const currentStep = ref(1); // 1 = form, 2 = resume
        const resumeData = ref(null);
        const waSending = ref(false);
        const waSent = ref(false);
        const waError = ref("");

        // ─── Form ──────────────────────────────────────────────────
        const form = useForm({
            nomer_hak: "",
            desa_kecamatan: "",
            nomor_hp: "",
            jenis_hak: "",
            file: null,
            nama_pemohon: "",
        });

        const uploadedFileName = ref("");

        function handleFileUpload(e) {
            const file = e.target.files[0];
            if (file) {
                form.file = file;
                uploadedFileName.value = file.name;
            }
        }

        const clearFile = () => {
            form.file = null;
            uploadedFileName.value = "";
            const input = document.getElementById("file");
            if (input) input.value = "";
        };

        // ─── Desa/Kecamatan Autocomplete ──────────────────────────
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
            const q = query.toLowerCase();
            filteredLocations.value = desaKecamatanList.filter((l) =>
                l.toLowerCase().includes(q),
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
            if (!showDropdown.value || !filteredLocations.value.length) return;
            if (e.key === "ArrowDown") {
                e.preventDefault();
                selectedIndex.value =
                    selectedIndex.value < filteredLocations.value.length - 1
                        ? selectedIndex.value + 1
                        : 0;
            } else if (e.key === "ArrowUp") {
                e.preventDefault();
                selectedIndex.value =
                    selectedIndex.value > 0
                        ? selectedIndex.value - 1
                        : filteredLocations.value.length - 1;
            } else if (e.key === "Enter") {
                e.preventDefault();
                const idx = selectedIndex.value > -1 ? selectedIndex.value : 0;
                if (filteredLocations.value[idx])
                    selectLocation(filteredLocations.value[idx]);
            } else if (e.key === "Escape") {
                showDropdown.value = false;
                selectedIndex.value = -1;
            }
        };

        const handleBlur = () =>
            setTimeout(() => {
                showDropdown.value = false;
            }, 200);

        // ─── Errors ────────────────────────────────────────────────
        const errors = ref({
            nomer_hak: "",
            desa_kecamatan: "",
            nomor_hp: "",
            jenis_hak: "",
        });

        // ─── Submit ────────────────────────────────────────────────
        const submitForm = () => {
            errors.value = {
                nomer_hak: "",
                desa_kecamatan: "",
                nomor_hp: "",
                jenis_hak: "",
                nama_pemohon: "",
            };
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
            if (hasError) return;

            form.nomer_hak = form.nomer_hak.replace(/^0+/, "");

            form.post("/genggam-berkas", {
                forceFormData: true,
                onSuccess: async (page) => {
                    const kode =
                        page.props?.flash?.kode_berkas ??
                        page.props?.kode_berkas ??
                        "BRK-" + Date.now().toString(36).toUpperCase();

                    resumeData.value = {
                        kode_berkas: kode,
                        nama_pemohon: form.nama_pemohon,
                        nomor_hp: form.nomor_hp,
                        desa_kecamatan: form.desa_kecamatan,
                        jenis_hak: form.jenis_hak,
                        nomer_hak: form.nomer_hak,
                        tanggal: new Date().toLocaleDateString("id-ID", {
                            day: "numeric",
                            month: "long",
                            year: "numeric",
                        }),
                    };

                    waSent.value = false;
                    waError.value = "";
                    currentStep.value = 2;

                    form.reset();
                    searchQuery.value = "";
                    uploadedFileName.value = "";

                    // ← Otomatis kirim WA setelah berkas tersimpan
                    await kirimWA();
                },
                onError: (errs) => console.error("Error submit:", errs),
            });
        };

        // ─── Kirim WA ──────────────────────────────────────────────
 

       const kirimWA = async () => {
    if (!resumeData.value) return;
    waSending.value = true;
    waError.value = "";

    const phone = "62" + resumeData.value.nomor_hp;
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(resumeData.value.kode_berkas)}&format=png`;

    const text =
        `✅ *Berkas Anda Telah Diterima*\n\n` +
        `📋 Kode Berkas: *${resumeData.value.kode_berkas}*\n` +
        `👤 Nama: ${resumeData.value.nama_pemohon || "-"}\n` +
        `📍 Lokasi: ${resumeData.value.desa_kecamatan}\n` +
        `📄 Jenis Hak: ${resumeData.value.jenis_hak} / No. ${resumeData.value.nomer_hak}\n` +
        `📅 Tanggal: ${resumeData.value.tanggal}\n\n` +
        `Gunakan kode berkas di atas untuk melacak status berkas Anda.\n` +
        `Terima kasih.`;

    try {
        // Kirim teks dulu
        await axios.post("/wa/send", { to: phone, text });

        // Kirim QR sebagai gambar
        await axios.post("/wa/send-image", {
            to: phone,
            text: `🔲 QR Code untuk kode berkas *${resumeData.value.kode_berkas}*`,
            image_url: qrUrl,
        });

        waSent.value = true;
    } catch (err) {
        waError.value = err.response?.data?.message || "Gagal mengirim WhatsApp.";
    } finally {
        waSending.value = false;
    }
};

        // ─── Reset ke form ─────────────────────────────────────────
        const inputBaru = () => {
            currentStep.value = 1;
            resumeData.value = null;
            waSent.value = false;
            waError.value = "";
        };

        return {
            form,
            errors,
            submitForm,
            handleFileUpload,
            uploadedFileName,
            clearFile,
            searchQuery,
            showDropdown,
            filteredLocations,
            selectedIndex,
            handleInput,
            selectLocation,
            handleKeydown,
            handleBlur,
            currentStep,
            resumeData,
            waSending,
            waSent,
            waError,
            kirimWA,
            inputBaru,
        };
    },
};
</script>

<template>
    <div class="gb-wrap">
        <div class="gb-container">
            <!-- ── Page Header ───────────────────────────────────── -->
            <div class="gb-page-header">
                <div>
                    <h1 class="gb-page-title">Input Data Berkas</h1>
                    <p class="gb-page-sub">
                        Daftarkan berkas pertanahan baru dengan mudah.
                    </p>
                </div>
                <!-- Step indicator -->
                <div class="gb-step-indicator">
                    <div
                        class="gb-step"
                        :class="{
                            active: currentStep >= 1,
                            done: currentStep > 1,
                        }"
                    >
                        <span class="gb-step-dot">
                            <svg
                                v-if="currentStep > 1"
                                xmlns="http://www.w3.org/2000/svg"
                                width="10"
                                height="10"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="3"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </span>
                        <span class="gb-step-label">Input Data</span>
                    </div>
                    <div
                        class="gb-step-line"
                        :class="{ active: currentStep > 1 }"
                    ></div>
                    <div class="gb-step" :class="{ active: currentStep >= 2 }">
                        <span class="gb-step-dot"></span>
                        <span class="gb-step-label">Resume & Kirim</span>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════
                 STEP 1 — FORM
            ══════════════════════════════════════════════════════ -->
            <form
                v-if="currentStep === 1"
                @submit.prevent="submitForm"
                class="gb-card"
                autocomplete="off"
            >
                <!-- 01 Data Pemohon -->
                <div class="gb-section">
                    <div class="gb-section-label">
                        <div class="gb-section-num">01</div>
                        <div>
                            <div class="gb-section-title">Data Pemohon</div>
                            <div class="gb-section-sub">
                                Informasi pemilik berkas
                            </div>
                        </div>
                    </div>
                    <div class="gb-fields">
                        <div class="gb-field">
                            <label class="gb-label" for="nama_pemohon"
                                >Nama Pemohon</label
                            >
                            <div class="gb-input-wrap">
                                <svg
                                    class="gb-input-icon"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                                <input
                                    type="text"
                                    id="nama_pemohon"
                                    v-model="form.nama_pemohon"
                                    placeholder="Nama lengkap pemohon"
                                    class="gb-input"
                                />
                            </div>
                        </div>
                        <div class="gb-field">
                            <label class="gb-label" for="nomor_hp"
                                >Nomor HP
                                <span class="gb-label-hint"
                                    >tanpa +62/0</span
                                ></label
                            >
                            <div class="gb-input-wrap">
                                <svg
                                    class="gb-input-icon"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                    />
                                </svg>
                                <span class="gb-input-prefix">+62</span>
                                <input
                                    type="text"
                                    id="nomor_hp"
                                    v-model="form.nomor_hp"
                                    placeholder="8xxxxxxxxxx"
                                    maxlength="12"
                                    @input="
                                        form.nomor_hp = form.nomor_hp
                                            .replace(/^0+/, '')
                                            .replace(/\D/g, '')
                                            .slice(0, 12)
                                    "
                                    class="gb-input gb-input-prefixed"
                                    required
                                />
                            </div>
                            <p v-if="errors.nomor_hp" class="gb-error">
                                {{ errors.nomor_hp }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="gb-divider"></div>

                <!-- 02 Data Bidang Tanah -->
                <div class="gb-section">
                    <div class="gb-section-label">
                        <div class="gb-section-num">02</div>
                        <div>
                            <div class="gb-section-title">
                                Data Bidang Tanah
                            </div>
                            <div class="gb-section-sub">
                                Lokasi dan hak atas tanah
                            </div>
                        </div>
                    </div>
                    <div class="gb-fields">
                        <!-- Desa/Kecamatan -->
                        <div class="gb-field gb-field-full">
                            <label class="gb-label" for="desa_kecamatan"
                                >Desa / Kecamatan</label
                            >
                            <div
                                class="gb-input-wrap"
                                style="position: relative"
                            >
                                <svg
                                    class="gb-input-icon"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                                <input
                                    type="text"
                                    id="desa_kecamatan"
                                    v-model="searchQuery"
                                    @input="handleInput"
                                    @focus="showDropdown = true"
                                    @blur="handleBlur"
                                    @keydown="handleKeydown"
                                    placeholder="Ketik nama desa atau kecamatan..."
                                    class="gb-input"
                                    required
                                    autocomplete="off"
                                />
                                <div
                                    v-if="
                                        showDropdown &&
                                        filteredLocations.length > 0
                                    "
                                    class="gb-dropdown"
                                >
                                    <ul>
                                        <li
                                            v-for="(
                                                loc, idx
                                            ) in filteredLocations"
                                            :key="loc"
                                            @click="selectLocation(loc)"
                                            :class="[
                                                'gb-dropdown-item',
                                                selectedIndex === idx
                                                    ? 'gb-dropdown-item-active'
                                                    : '',
                                            ]"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="12"
                                                height="12"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                style="
                                                    flex-shrink: 0;
                                                    opacity: 0.4;
                                                "
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                                />
                                            </svg>
                                            {{ loc }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <p v-if="errors.desa_kecamatan" class="gb-error">
                                {{ errors.desa_kecamatan }}
                            </p>
                        </div>
                        <!-- Jenis Hak -->
                        <div class="gb-field">
                            <label class="gb-label" for="jenis_hak"
                                >Jenis Hak</label
                            >
                            <div class="gb-input-wrap">
                                <svg
                                    class="gb-input-icon"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                                <select
                                    id="jenis_hak"
                                    v-model="form.jenis_hak"
                                    class="gb-input gb-select"
                                    required
                                >
                                    <option value="" disabled>
                                        Pilih Jenis Hak
                                    </option>
                                    <option value="HGB">
                                        HGB — Hak Guna Bangunan
                                    </option>
                                    <option value="HM">HM — Hak Milik</option>
                                    <option value="HMRS">
                                        HMRS — Hak Milik Rumah Susun
                                    </option>
                                    <option value="HP">HP — Hak Pakai</option>
                                    <option value="HW">HW — Hak Wakaf</option>
                                </select>
                            </div>
                            <p v-if="errors.jenis_hak" class="gb-error">
                                {{ errors.jenis_hak }}
                            </p>
                        </div>
                        <!-- Nomer Hak -->
                        <div class="gb-field">
                            <label class="gb-label" for="nomer_hak"
                                >Nomor Hak
                                <span class="gb-label-hint"
                                    >maks. 5 digit</span
                                ></label
                            >
                            <div class="gb-input-wrap">
                                <svg
                                    class="gb-input-icon"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"
                                    />
                                </svg>
                                <input
                                    type="text"
                                    id="nomer_hak"
                                    v-model="form.nomer_hak"
                                    maxlength="5"
                                    placeholder="00000"
                                    @input="
                                        form.nomer_hak = form.nomer_hak
                                            .replace(/\D/g, '')
                                            .slice(0, 5)
                                    "
                                    class="gb-input gb-input-mono"
                                    required
                                />
                            </div>
                            <p v-if="errors.nomer_hak" class="gb-error">
                                {{ errors.nomer_hak }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="gb-divider"></div>

                <!-- 03 Dokumen -->
                <div class="gb-section">
                    <div class="gb-section-label">
                        <div class="gb-section-num">03</div>
                        <div>
                            <div class="gb-section-title">
                                Dokumen Pendukung
                            </div>
                            <div class="gb-section-sub">
                                Opsional — PDF/JPG/PNG maks. 5MB
                            </div>
                        </div>
                    </div>
                    <div class="gb-fields">
                        <div class="gb-field gb-field-full">
                            <div
                                class="gb-upload-zone"
                                @click="$refs.fileInput.click()"
                                @dragover.prevent
                                @drop.prevent="handleDrop"
                            >
                                <input
                                    type="file"
                                    ref="fileInput"
                                    id="file"
                                    name="file"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    @change="handleFileUpload"
                                    style="display: none"
                                />
                                <div
                                    v-if="!uploadedFileName"
                                    class="gb-upload-idle"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="32"
                                        height="32"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                        />
                                    </svg>
                                    <p class="gb-upload-text">
                                        Klik atau drag & drop
                                    </p>
                                    <p class="gb-upload-hint">
                                        PDF · JPG · PNG — Maks. 5MB
                                    </p>
                                </div>
                                <div v-else class="gb-upload-filled">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="20"
                                        height="20"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <span>{{ uploadedFileName }}</span>
                                    <button
                                        type="button"
                                        class="gb-upload-remove"
                                        @click.stop="clearFile"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="gb-footer">
                    <div class="gb-footer-note">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="14"
                            height="14"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        Field wajib harus diisi
                    </div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="gb-submit"
                    >
                        <span
                            v-if="!form.processing"
                            style="
                                display: flex;
                                align-items: center;
                                gap: 0.4rem;
                            "
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                            Simpan Berkas
                        </span>
                        <span v-else class="gb-submit-loading">
                            <svg
                                class="gb-spin"
                                xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                />
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>

            <!-- ══════════════════════════════════════════════════════
                 STEP 2 — RESUME
            ══════════════════════════════════════════════════════ -->
            <div
                v-else-if="currentStep === 2 && resumeData"
                class="gb-card resume-card"
            >
                <!-- Header resume -->
                <div class="resume-header">
                    <div class="resume-success-icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="28"
                            height="28"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h2 class="resume-title">Berkas Berhasil Disimpan</h2>
                        <p class="resume-sub">{{ resumeData.tanggal }}</p>
                    </div>
                </div>

                <div class="resume-body">
                    <!-- Kode berkas + QR -->
                    <div class="resume-kode-wrap">
                        <div class="resume-kode-info">
                            <div class="resume-kode-label">Kode Berkas</div>
                            <div class="resume-kode-value">
                                {{ resumeData.kode_berkas }}
                            </div>
                            <div class="resume-kode-hint">
                                Sampaikan kode ini kepada pemohon untuk
                                pelacakan berkas.
                            </div>
                        </div>
                        <!-- QR code menggunakan Google Charts API -->
                        <div class="resume-qr">
                            <img
                                :src="`https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=${encodeURIComponent(resumeData.kode_berkas)}&format=png&margin=8`"
                                :alt="resumeData.kode_berkas"
                                width="140"
                                height="140"
                            />
                            <span class="resume-qr-label"
                                >Scan untuk kode berkas</span
                            >
                        </div>
                    </div>

                    <div class="gb-divider" style="margin: 0"></div>

                    <!-- Data detail -->
                    <div class="resume-detail-grid">
                        <div class="resume-detail-item">
                            <span class="resume-detail-label"
                                >Nama Pemohon</span
                            >
                            <span class="resume-detail-value">{{
                                resumeData.nama_pemohon || "—"
                            }}</span>
                        </div>
                        <div class="resume-detail-item">
                            <span class="resume-detail-label">Nomor HP</span>
                            <span class="resume-detail-value"
                                >+62{{ resumeData.nomor_hp }}</span
                            >
                        </div>
                        <div class="resume-detail-item">
                            <span class="resume-detail-label"
                                >Desa / Kecamatan</span
                            >
                            <span class="resume-detail-value">{{
                                resumeData.desa_kecamatan
                            }}</span>
                        </div>
                        <div class="resume-detail-item">
                            <span class="resume-detail-label">Jenis Hak</span>
                            <span class="resume-detail-value">{{
                                resumeData.jenis_hak
                            }}</span>
                        </div>
                        <div class="resume-detail-item">
                            <span class="resume-detail-label">Nomor Hak</span>
                            <span class="resume-detail-value">{{
                                resumeData.nomer_hak
                            }}</span>
                        </div>
                        <div class="resume-detail-item">
                            <span class="resume-detail-label"
                                >Tanggal Input</span
                            >
                            <span class="resume-detail-value">{{
                                resumeData.tanggal
                            }}</span>
                        </div>
                    </div>

                    <div class="gb-divider" style="margin: 0"></div>

                    <!-- WA Section -->
                    <div class="resume-wa-section">
                        <div class="resume-wa-info">
                            <div class="resume-wa-title">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="#25D366"
                                >
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"
                                    />
                                </svg>
                                Kirim Notifikasi WhatsApp
                            </div>
                            <p class="resume-wa-desc">
                                Kirim kode berkas dan detail ke nomor
                                <strong>+62{{ resumeData.nomor_hp }}</strong>
                                via WhatsApp.
                            </p>
                        </div>

                        <!-- Status -->
                        <div v-if="waSent" class="resume-wa-status success">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            Pesan berhasil dikirim!
                        </div>
                        <div v-if="waError" class="resume-wa-status error">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            {{ waError }}
                        </div>

                        <button
                            v-if="!waSent"
                            @click="kirimWA"
                            :disabled="waSending"
                            class="btn-wa"
                        >
                            <span
                                v-if="!waSending"
                                style="
                                    display: flex;
                                    align-items: center;
                                    gap: 0.5rem;
                                "
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="white"
                                >
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"
                                    />
                                </svg>
                                Kirim via WhatsApp
                            </span>
                            <span v-else class="gb-submit-loading">
                                <svg
                                    class="gb-spin"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                    />
                                </svg>
                                Mengirim...
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Footer resume -->
                <div class="gb-footer">
                    <p class="gb-footer-note">
                        Berkas telah tersimpan di sistem.
                    </p>
                    <button type="button" @click="inputBaru" class="gb-submit">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                        Input Berkas Baru
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap");

*,
*::before,
*::after {
    box-sizing: border-box;
}

.gb-wrap {
    font-family: "Plus Jakarta Sans", sans-serif;
    min-height: 100vh;
    background: #f4f6fb;
    padding: 2.5rem 1rem 4rem;
}
.gb-container {
    max-width: 780px;
    margin: 0 auto;
}

/* Header */
.gb-page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.75rem;
    flex-wrap: wrap;
    gap: 1.5rem;
}
.gb-page-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.02em;
    margin-bottom: 0.3rem;
}
.gb-page-sub {
    font-size: 0.875rem;
    color: #64748b;
}

/* Step indicator */
.gb-step-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-top: 0.25rem;
}
.gb-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
}
.gb-step-dot {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    color: white;
}
.gb-step.active .gb-step-dot {
    background: #2563eb;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
}
.gb-step.done .gb-step-dot {
    background: #16a34a;
}
.gb-step-label {
    font-size: 0.68rem;
    color: #94a3b8;
    white-space: nowrap;
}
.gb-step.active .gb-step-label {
    color: #2563eb;
    font-weight: 600;
}
.gb-step.done .gb-step-label {
    color: #16a34a;
}
.gb-step-line {
    width: 48px;
    height: 2px;
    background: #e2e8f0;
    margin-bottom: 1.1rem;
    border-radius: 2px;
    transition: background 0.3s;
}
.gb-step-line.active {
    background: #2563eb;
}

/* Card */
.gb-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e8ecf4;
    box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06);
    overflow: hidden;
    animation: fadeUp 0.4s ease both;
}

/* Section */
.gb-section {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 2rem;
    padding: 2rem;
}
.gb-section-label {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    padding-top: 0.2rem;
}
.gb-section-num {
    font-family: "JetBrains Mono", monospace;
    font-size: 0.72rem;
    font-weight: 500;
    color: #2563eb;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 0.2rem 0.45rem;
    border-radius: 5px;
    flex-shrink: 0;
    margin-top: 2px;
}
.gb-section-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.2rem;
}
.gb-section-sub {
    font-size: 0.78rem;
    color: #94a3b8;
    line-height: 1.4;
}
.gb-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 0 2rem;
}

/* Fields */
.gb-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.1rem;
}
.gb-field-full {
    grid-column: 1 / -1;
}
.gb-field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}
.gb-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.gb-label-hint {
    font-size: 0.72rem;
    font-weight: 400;
    color: #94a3b8;
}

/* Input */
.gb-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.gb-input-icon {
    position: absolute;
    left: 0.75rem;
    color: #9ca3af;
    pointer-events: none;
    z-index: 1;
}
.gb-input {
    width: 100%;
    padding: 0.65rem 0.85rem 0.65rem 2.4rem;
    font-size: 0.875rem;
    font-family: "Plus Jakarta Sans", sans-serif;
    color: #1e293b;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    outline: none;
    transition:
        border-color 0.2s,
        box-shadow 0.2s,
        background 0.2s;
    appearance: none;
}
.gb-input:focus {
    border-color: #2563eb;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.gb-input::placeholder {
    color: #c0c8d8;
}
.gb-input-prefix {
    position: absolute;
    left: 2.4rem;
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
    pointer-events: none;
    z-index: 1;
}
.gb-input-prefixed {
    padding-left: 4.2rem !important;
}
.gb-select {
    cursor: pointer;
    padding-right: 2rem;
}
.gb-input-mono {
    font-family: "JetBrains Mono", monospace !important;
    letter-spacing: 0.1em;
    font-size: 1rem;
}
.gb-error {
    font-size: 0.75rem;
    color: #ef4444;
    margin-top: 0.1rem;
}

/* Dropdown */
.gb-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(15, 23, 42, 0.12);
    z-index: 50;
    overflow: hidden;
}
.gb-dropdown ul {
    list-style: none;
    padding: 0.4rem;
    margin: 0;
    max-height: 220px;
    overflow-y: auto;
}
.gb-dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 0.75rem;
    font-size: 0.83rem;
    color: #374151;
    cursor: pointer;
    border-radius: 7px;
    transition: background 0.15s;
}
.gb-dropdown-item:hover {
    background: #f1f5f9;
}
.gb-dropdown-item-active {
    background: #eff6ff;
    color: #2563eb;
}

/* Upload */
.gb-upload-zone {
    border: 2px dashed #d1d9e6;
    border-radius: 14px;
    padding: 2rem;
    cursor: pointer;
    background: #f8fafc;
    transition:
        border-color 0.2s,
        background 0.2s;
    text-align: center;
}
.gb-upload-zone:hover {
    border-color: #2563eb;
    background: #f0f6ff;
}
.gb-upload-idle {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.6rem;
    color: #94a3b8;
}
.gb-upload-text {
    font-size: 0.875rem;
    font-weight: 500;
    color: #475569;
}
.gb-upload-hint {
    font-size: 0.75rem;
    color: #94a3b8;
}
.gb-upload-filled {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    color: #16a34a;
    font-size: 0.875rem;
    font-weight: 500;
}
.gb-upload-remove {
    font-size: 0.75rem;
    color: #ef4444;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.2rem 0.5rem;
    border-radius: 5px;
    transition: background 0.15s;
}
.gb-upload-remove:hover {
    background: #fef2f2;
}

/* Footer */
.gb-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap;
    gap: 1rem;
}
.gb-footer-note {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.78rem;
    color: #94a3b8;
}

/* Submit btn */
.gb-submit {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #1d4ed8;
    color: #fff;
    font-family: "Plus Jakarta Sans", sans-serif;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.75rem 1.75rem;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 12px rgba(29, 78, 216, 0.3);
}
.gb-submit:hover:not(:disabled) {
    background: #1e40af;
    transform: translateY(-1px);
    box-shadow: 0 4px 20px rgba(29, 78, 216, 0.4);
}
.gb-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.gb-submit-loading {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* ── RESUME ── */
.resume-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.75rem 2rem;
    border-bottom: 1px solid #f1f5f9;
}
.resume-success-icon {
    width: 52px;
    height: 52px;
    background: #dcfce7;
    color: #16a34a;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.resume-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.2rem;
}
.resume-sub {
    font-size: 0.8rem;
    color: #94a3b8;
}
.resume-body {
    padding: 2rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Kode + QR */
.resume-kode-wrap {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.resume-kode-label {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #94a3b8;
    margin-bottom: 0.4rem;
}
.resume-kode-value {
    font-family: "JetBrains Mono", monospace;
    font-size: 1.8rem;
    font-weight: 700;
    color: #1d4ed8;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}
.resume-kode-hint {
    font-size: 0.78rem;
    color: #64748b;
    max-width: 260px;
    line-height: 1.5;
}
.resume-qr {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}
.resume-qr img {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}
.resume-qr-label {
    font-size: 0.7rem;
    color: #94a3b8;
}

/* Detail grid */
.resume-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
.resume-detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}
.resume-detail-label {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: #94a3b8;
}
.resume-detail-value {
    font-size: 0.9rem;
    font-weight: 500;
    color: #1e293b;
}

/* WA Section */
.resume-wa-section {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 14px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.resume-wa-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: #15803d;
    margin-bottom: 0.3rem;
}
.resume-wa-desc {
    font-size: 0.83rem;
    color: #4b7a5e;
}
.btn-wa {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #25d366;
    color: #fff;
    font-family: "Plus Jakarta Sans", sans-serif;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    align-self: flex-start;
    box-shadow: 0 2px 12px rgba(37, 211, 102, 0.3);
}
.btn-wa:hover:not(:disabled) {
    background: #1db954;
    transform: translateY(-1px);
}
.btn-wa:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.resume-wa-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.83rem;
    font-weight: 500;
    padding: 0.6rem 0.9rem;
    border-radius: 8px;
}
.resume-wa-status.success {
    background: #dcfce7;
    color: #16a34a;
}
.resume-wa-status.error {
    background: #fef2f2;
    color: #dc2626;
}

/* Animations */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.gb-spin {
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Dropdown scrollbar */
.gb-dropdown ul::-webkit-scrollbar {
    width: 4px;
}
.gb-dropdown ul::-webkit-scrollbar-track {
    background: transparent;
}
.gb-dropdown ul::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 4px;
}

@media (max-width: 640px) {
    .gb-section {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 1.5rem;
    }
    .gb-fields {
        grid-template-columns: 1fr;
    }
    .gb-page-header {
        flex-direction: column;
    }
    .gb-step-indicator {
        display: none;
    }
    .resume-kode-wrap {
        flex-direction: column;
    }
    .resume-detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>
