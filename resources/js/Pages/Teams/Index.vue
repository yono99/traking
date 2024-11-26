<template>
  <div class="dark:text-white">
    <h1>Teams</h1>
    <!-- hanya bisa di klik 1 kali -->
    <form @submit.prevent="createTeam" class="mb-4 ">
      <input v-model="teamName" placeholder="Team name" class="text-gray-800"/>
      <button type="submit">Create Team</button>
    </form>

    <h1 class="text-2xl font-bold mb-4">Daftar Teams</h1>
    <table class="table-auto w-full border-collapse border border-gray-300">
      <thead>
        <tr class="bg-gray-400">
          <th class="border border-gray-300 px-4 py-2"></th>
          <th class="border border-gray-300 px-4 py-2">Nama Team</th>
          <th class="border border-gray-300 px-4 py-2">Anggota</th>
          <th class="border border-gray-300 px-4 py-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="teams.length == 0">
            <td colspan="4" class="border border-gray-300 px-4 py-2 text-center">Tidak ada Team</td>
        </tr>
        <tr v-for="(team, index) in teams" :key="team.id">
          <td class="border border-gray-300 px-4 py-2 text-center">{{ index + 1 }}</td>
          <td class="border border-gray-300 px-4 py-2">{{ team.name }}</td>
          <td class="border border-gray-300 px-4 py-2">
            <ul class="list-disc pl-4">
              <li v-if="team.users.length == 0">Tidak ada anggota</li>
              <li v-else v-for="user in team.users" :key="user.id">{{ user.name }}</li>
              <!-- add user with select option-->
               <div >
                  <form @submit.prevent="addUserToTeam(team.id)">
                    <select v-model="user_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                      <option disabled value="">Pilih anggota</option>
                      <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                    <button class="bg-blue-500 text-white px-2 py-1 rounded" type="submit">Add</button>
                  </form>
               </div>
            </ul>
          </td>
          <td class="border border-gray-300 px-4 py-2">
            <!-- delete Team -->
            <button class="bg-red-500 text-white px-2 py-1 rounded"
            @click="deleteTeam(team.id)">
                Hapus
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
export default {
  layout: AppLayout,
  props: {
    teams: Array,
    users: Array // Data yang dikirim dari backend
  },
  data() {
    return {
      teamName: '', // Nama tim yang dimasukkan pengguna
      errorMessage: '', // Pesan kesalahan jika ada
    };
  },
  methods: {
    createTeam() {
        //saya ingin buttonnya di disable ketika di klik 1 kali
        document.querySelector('button[type="submit"]').disabled = true;
      // Validasi nama tim
      if (this.teamName.trim() === '') {
        this.errorMessage = "Nama tim tidak boleh kosong.";
        return;
      }

      // Kirim data tim ke server menggunakan fetch
      fetch('/teams', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
          name: this.teamName, // Nama tim yang dimasukkan oleh pengguna
        }),
      })
      .then((response) => {
        document.querySelector('button[type="submit"]').disabled = false;
        if (!response.ok) {
          throw new Error('Gagal membuat tim.');
        }
        return response.json();
      })
      .then((data) => {
        // Tindakan setelah berhasil membuat tim, misalnya reset input dan beri notifikasi
        this.teamName = ''; // Reset nama tim
        alert(data.message); // Menampilkan pesan dari server
        this.$inertia.reload(); // Refresh halaman
      })
      .catch((error) => {
        console.error('Error saat membuat tim:', error);
        this.errorMessage = 'Terjadi kesalahan saat membuat tim.';
      });
    },
    deleteTeam(teamId) {
        //alert
        if(confirm("Apakah Anda yakin ingin menghapus tim ini? kamu tidak bisa hapus tim ini jika tim ini memiliki anggota")){
            // Kirim permintaan untuk menghapus tim ke server menggunakan fetch
            fetch(`/teams/${teamId}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              },
            })
            .then((response) => {
              if (!response.ok) {
                throw new Error('Gagal menghapus tim.');
              }
              return response.json();
            })
            .then((data) => {
              // Tindakan setelah berhasil menghapus tim, misalnya refresh halaman
              this.$inertia.reload(); // Refresh halaman
            })
            .catch((error) => {
              console.error('Error saat menghapus tim:', error);
              this.errorMessage = 'Terjadi kesalahan saat menghapus tim.';
            });
        }
    },

    addUserToTeam(teamId) {
      // Kirim permintaan untuk menambahkan anggota ke tim ke server menggunakan fetch
      fetch(`/teams/${teamId}/add-member`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
          user_id: this.user_id, // ID anggota yang akan ditambahkan ke tim
        }),
      })
      .then((response) => {
        if (!response.ok) {
          throw new Error('Gagal menambahkan anggota ke tim.');
        }
        return response.json();
      })
      .then((data) => {
        // Tindakan setelah berhasil menambahkan anggota ke tim, misalnya refresh halaman
        this.$inertia.reload(); // Refresh halaman
      })
      .catch((error) => {
        console.error('Error saat menambahkan anggota ke tim:', error);
        this.errorMessage = 'Terjadi kesalahan saat menambahkan anggota ke tim.';
      });
    },
  },
};
</script>
<style scoped>
.table-auto {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  text-align: left;
  vertical-align: top;
}
</style>
