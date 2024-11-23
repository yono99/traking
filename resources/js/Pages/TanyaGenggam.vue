<template>
  <div class="search-container">
    <h1>Pencarian Tanya Genggam</h1>
    <input type="text" v-model="nomorHak" placeholder="Masukkan Nomer Hak" @keyup.enter="search" />
    <button @click="search">Cari</button>

    <!-- Tabel hasil pencarian -->
    <table v-if="landBooks.length" class="result-table">
  <thead>
    <tr>
      <th>Nomer Hak</th>
      <th>Jenis Hak</th>
      <th>Desa/Kecamatan</th>
      <th>Nama Service</th>
      <th>Keterangan</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="landBook in landBooks" :key="landBook.id">
      <td>{{ landBook.nomer_hak }}</td>
      <td>{{ landBook.jenis_hak }}</td>
      <td>{{ landBook.desa_kecamatan }}</td>
      <td>{{ getService(landBook.id)?.name || "N/A" }}</td>
      <td>{{ getService(landBook.id)?.remarks || "N/A" }}</td>
      <td>
        <button
          @click="updateStatus(getService(landBook.id)?.id)"
          :disabled="!getService(landBook.id)?.id"
          class="btn-update"
        >
          Update Status
        </button>
      </td>
    </tr>
  </tbody>
</table>


    <div v-if="errorMessage" class="error-message">
      {{ errorMessage }}
    </div>
  </div>
</template>
<script>
import { ref } from "vue";

export default {
  setup() {
    const nomorHak = ref("");
    const landBooks = ref([]);
    const services = ref([]);
    const errorMessage = ref("");

    const search = () => {
      landBooks.value = [];
      services.value = [];
      errorMessage.value = "";

      fetch(`/search?nomer_hak=${encodeURIComponent(nomorHak.value)}`)
        .then((response) => {
          if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
          return response.json();
        })
        .then((data) => {
          landBooks.value = data.landBooks || [];
          services.value = data.services || [];
        })
        .catch((error) => {
          console.error("Error saat mencari data:", error);
          errorMessage.value = "Terjadi kesalahan saat mencari data. Silakan coba lagi.";
        });
    };

    const updateStatus = (serviceId) => {
      if (!serviceId) {
        errorMessage.value = "Service ID tidak valid.";
        return;
      }

      fetch(`/update-status`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify({ service_id: serviceId, status: "UPDATED" }),
      })
        .then((response) => {
          if (!response.ok) throw new Error("Gagal memperbarui status.");
          return response.json();
        })
        .then((data) => {
          alert(data.message);
          search(); // Refresh data
        })
        .catch((error) => {
          console.error("Error saat memperbarui status:", error);
          errorMessage.value = "Terjadi kesalahan saat memperbarui status.";
        });
    };

<<<<<<< HEAD
<<<<<<< HEAD
    const getService = (landBookId) => {
      return services.value.find((service) => service.land_book_id === landBookId);
    };
=======
=======
>>>>>>> parent of b8f48d4 (Update TanyaGenggam.vue)
            fetch(`https://traking.test/update-status`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ service_id: serviceId, status: statusUpdate }),
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.text().then((text) => {
                            console.error("Backend response:", text);
                            throw new Error(`Error: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then((data) => {
                    alert(data.message);
                    search(); // Refresh data
                })
                .catch((error) => {
                    console.error("Error saat memperbarui status:", error);
                    errorMessage.value = error.message || "Terjadi kesalahan saat memperbarui status.";
                });
        };
>>>>>>> parent of b8f48d4 (Update TanyaGenggam.vue)

    return { nomorHak, landBooks, services, errorMessage, search, updateStatus, getService };
  },
};
</script>
<style scoped>
.result-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  font-family: Arial, sans-serif;
}

.result-table th {
  background-color: #007BFF;
  color: white;
  padding: 10px;
  text-align: left;
}

.result-table td {
  border: 1px solid #ddd;
  padding: 10px;
}

.result-table tr:nth-child(even) {
  background-color: #f9f9f9;
}

.result-table tr:hover {
  background-color: #f1f1f1;
}

.btn-update {
  background-color: #28a745;
  color: white;
  border: none;
  padding: 5px 10px;
  cursor: pointer;
  border-radius: 4px;
  font-size: 14px;
}

.btn-update:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.btn-update:hover:not(:disabled) {
  background-color: #218838;
}

</style>
