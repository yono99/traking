<template>
  <div>
    <h1 class="text-2xl font-semibold">Input Data Buku Tanah</h1>
    
    <form @submit.prevent="submitForm" class="mt-4">
      <div class="mb-4">
        <label for="nomer_hak" class="block">Nomor Hak</label>
        <input type="text" id="nomer_hak" v-model="form.nomer_hak" class="mt-1 block w-full" required />
      </div>
      
      <div class="mb-4">
        <label for="jenis_hak" class="block">Jenis Hak</label>
        <input type="text" id="jenis_hak" v-model="form.jenis_hak" class="mt-1 block w-full" required />
      </div>
      
      <div class="mb-4">
        <label for="desa" class="block">Desa</label>
        <input type="text" id="desa" v-model="form.desa" class="mt-1 block w-full" required />
      </div>
      
      <div class="mb-4">
        <label for="kecamatan" class="block">Kecamatan</label>
        <input type="text" id="kecamatan" v-model="form.kecamatan" class="mt-1 block w-full" required />
      </div>

      <div class="mb-4">
        <label for="status_alih_media" class="block">Status Alih Media</label>
        <input type="checkbox" id="status_alih_media" v-model="form.status_alih_media" />
      </div>
      
      <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Simpan Data</button>
    </form>
  </div>
</template>

<script>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

export default {
  props: {
    user: Object,
  },

  setup() {
    const form = ref({
      nomer_hak: '',
      jenis_hak: '',
      desa: '',
      kecamatan: '',
      status_alih_media: false,
    });

    const submitForm = () => {
      Inertia.post(route('landbook.store'), form.value);
    };

    return { form, submitForm };
  },
};
</script>

<style scoped>
/* Styling for form elements */
</style>
