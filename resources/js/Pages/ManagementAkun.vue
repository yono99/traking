<script>
import { Inertia } from "@inertiajs/inertia";
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
    layout: AppLayout, // Gunakan layout
    data() {
        return {
            form: {
                email: "",
                unit: "",
                role: "",
            },
            message: "",
        };
    },
    methods: {
        updateAccount() {
            Inertia.post("/management-akun/update", this.form, {
                onSuccess: () => {
                    this.message = "Akun berhasil diperbarui!";
                },
                onError: (errors) => {
                    this.message = "Terjadi kesalahan, periksa kembali form!";
                },
            });
        },
    },
};
</script>
<template>
    <div>
        <h1>Management Akun</h1>
        <form @submit.prevent="updateAccount">
            <div>
                <label for="email">Email:</label>
                <input v-model="form.email" type="email" id="email" required />
            </div>

            <div>
                <label for="unit">Unit:</label>
                <select v-model="form.unit" id="unit" required>
                    <option value="loket">loket</option>
                    <option value="verifikator">verifikator</option>
                    <option value="bukutanah">Buku tanah</option>
                    <option value="sps">SPS</option>
                    <option value="pengukuran">pengukuran</option>
                    <option value="pelaksana_bn">pelaksana - BN</option>
                    <option value="pelaksana_ph">pelaksana - PH</option>
                    <option value="pelaksana_roya">pelaksana - ROYA</option>
                    <option value="pelaksana_ph_ruko">pelaksana - PH RUKO</option>
                    <option value="pelaksana_sk">pelaksana - SK</option>
                    <option value="QC">QC</option>
                    <option value="QC_bn">QC - BN</option>
                    <option value="QC_ph">QC - PH</option>
                    <option value="QC_roya">QC - ROYA</option>
                    <option value="QC_ph_ruko">QC - PH RUKO</option>
                    <option value="QC_sk">QC - SK</option>
                    <option value="pengesahan">Pengesahan</option>
                    <option value="bensus">bensus</option>
                    <option value="paraf">Pemeriksaan Berkas/paraf</option>
                    <option value="TTE_PRODUK_LAYANAN">
                        TTE Produk Layanan
                    </option>
                     <option value="loket_penyerahan">
                        loket penyerahan
                    </option>
                </select>
            </div>

            <div>
                <label for="role">Role:</label>
                <select v-model="form.role" id="role" required>
                    <option value="admin">Admin</option>
                    <option value="umum">Umum</option>
                </select>
            </div>

            <button type="submit">Update</button>
        </form>

        <div v-if="message">
            <p>{{ message }}</p>
        </div>
    </div>
</template>

<style>
/* Tambahkan styling sesuai kebutuhan */
form div {
    margin-bottom: 15px;
}

label {
    display: block;
    font-weight: bold;
}

input,
select,
button {
    padding: 8px;
    width: 100%;
    max-width: 300px;
}
</style>
