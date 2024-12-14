<template>
  <div class="status-container">
    <!-- Bagian Status Layanan -->
    <div class="box-container">
       <div class="box">
        <span class="label">Total Proses:</span>
        <span class="count">{{ countProses }}</span>
      </div>
      <div class="box">
        <span class="label">Total Selesai TTE:</span>
        <span class="count">{{ countSelesaiTTE }}</span>
      </div>
      
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      countSelesaiTTE: 0,
      countProses: 0,
    };
  },
  mounted() {
    this.fetchServiceCounts();
  },
  methods: {
    async fetchServiceCounts() {
      try {
        const response = await axios.get('/api/services/count');
        this.countSelesaiTTE = response.data.countSelesaiTTE;
        this.countProses = response.data.countProses;
      } catch (error) {
        console.error("Error fetching counts:", error);
      }
    },
  },
};
</script>

<style scoped>
/* Styling untuk menempatkan komponen di tengah layar */
.status-container {
  display: flex;
  justify-content: center;
  align-items: center;
 
}

.box-container {
  display: grid;
  grid-template-columns: repeat(2, 1fr); /* Dua kolom dengan ukuran yang sama */
  justify-items: center; /* Memastikan semua elemen kotak berada di tengah */
   
}

.box {
  background-color: #ffffff;
  border: 1px solid #ddd;
  padding: 1rem;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 150px; /* Menentukan lebar kotak */
}

.label {
  font-weight: bold;
  color: #333;
  margin-bottom: 0.5rem;
}

.count {
  font-size: 1.25rem;
  color: #007BFF;
  font-weight: 500;
}
</style>
