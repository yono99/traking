<script>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
export default {
   layout: AppLayout, // Gunakan layout
  data() {
    return {
      form: {
        email: '',
        unit: '',
        role: '',
      },
      message: '',
    };
  },
  methods: {
    updateAccount() {
      Inertia.post('/management-akun/update', this.form, {
        onSuccess: () => {
          this.message = 'Akun berhasil diperbarui!';
        },
        onError: (errors) => {
          this.message = 'Terjadi kesalahan, periksa kembali form!';
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
          <option value="Survei dan Pemetaan">Survei dan Pemetaan</option>
          <option value="Penetapan Hak">Penetapan Hak</option>
          <option value="Penetapan Tanah Pemerintah">Penetapan Tanah Pemerintah</option>
          <option value="Pendaftaran Tanah">Pendaftaran Tanah</option>
          <option value="Peralihan Hak">Peralihan Hak</option>
          <option value="loket">Loket</option>
          <option value="sps">SPS</option>
          <option value="verifikator">Verifikator</option>
          <option value="QC">QC</option>
          <option value="pengesahan">Pengesahan</option>
          <option value="paraf">Paraf</option>
          <option value="pelaksana">Pelaksana</option>
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

input, select, button {
  padding: 8px;
  width: 100%;
  max-width: 300px;
}
</style>
