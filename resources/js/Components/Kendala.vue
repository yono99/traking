<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center">
            <!-- Modal backdrop -->
            <div class="fixed inset-0 bg-black opacity-25"></div>

            <!-- Modal content -->
            <div
                class="relative bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl mx-4"
            >
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Update Data</h3>
                    <button
                        @click="closeModal"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <span class="sr-only">Tutup</span>
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 
                        <!-- status-->
                        <div  >
                            <label
                                class="block text-sm font-medium text-gray-700"
                                 
                                >Status</label
                            >
                            <select
                                
                                v-model="form.status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option
                                    v-for="status in statusOptions"
                                    :key="status"
                                    :value="status"
                                >
                                    {{ status }}
                                </option>
                            </select>
                        </div>
                        <!-- Keterangan -->
                        <div >
                            <label
                                 
                                class="block text-sm font-medium text-gray-700"
                                >Keterangan</label
                            >
                            <textarea
                                 
                                v-model="form.remarks"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            ></textarea>
                        </div>
                       
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? "Updating..." : "Update" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    show: Boolean,
    serviceId: Number,
    service: Object,
    landBook: Object,
});

const emit = defineEmits(["close"]);

const statusOptions = [
    "TERKENDALA",
     
];

const form = useForm({
    status: "",
    remarks: "",
    
});

// Inisialisasi form dengan data yang ada
watch(
    () => props.show,
    (newVal) => {
        if (newVal && props.service) {
            form.status = props.service.status || "";
             
       
            
        }
    }
);

// UpdateModal.vue
const submitForm = async () => {
    try {
        await axios.post(`/inventory/update-status/${props.serviceId}`, {
            status: form.status,
            remarks: form.remarks,
            
            _token: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"), // Tambahkan ini
        });

        closeModal();
        window.location.reload();
    } catch (error) {
        console.error("Error updating data:", error);
        alert(error.response?.data?.message || "Gagal mengupdate data");
    }
};

const closeModal = () => {
    form.reset();
    emit("close");
};
</script>
